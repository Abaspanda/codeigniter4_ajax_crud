<?php

namespace App\Controllers;

use App\Models\UserModel;

use function PHPUnit\Framework\directoryExists;

class Home extends BaseController
{
    private $user;
    protected $db;

    public function __construct()
    {
        // Loading the database service via dependency injection
        $this->db = \Config\Database::connect();
        $this->user = new UserModel();
        helper(['html', 'form']);
    }

    public function index(): string
    {
        return view('index');
    }

    // Add user
    public function adduser()
    {
        // Handle profile image upload
        $profileImage = $this->handleProfileImageUpload();

        // Prepare user data
        $userData = [
            'firstname' => $this->request->getPost('firstname'),
            'lastname' => $this->request->getPost('lastname'),
            'mobile' => $this->request->getPost('mobile'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'address' => $this->request->getPost('address'),
            'profile' => $profileImage
        ];

        // Insert the user data into the database
        $this->user->insert($userData);

        // Prepare sweet alert response data
        $response = [
            'status' => 'success',
            'message' => 'User added successfully'
        ];

        return $this->response->setJSON($response);
    }

    // Fetch all users
    public function fetchUsers()
    {
        // Select user's table
        $builder = $this->db->table('users');

        // Columns to select | (*) means select everything
        $builder->select('*');

        // Handle search value
        $searchData = $this->request->getPost('search');
        if (isset($searchData['value']) && !empty($searchData['value'])) {
            $searchValue = $searchData['value'];
            $builder->groupStart()
                ->like('id', $searchValue)
                ->orLike('firstname', $searchValue)
                ->orLike('lastname', $searchValue)
                ->orLike('email', $searchValue)
                ->groupEnd();
        }

        // Handle sorting
        $order = $this->request->getPost('order');
        if ($order) {
            $column = $order[0]['column'];
            $dir = $order[0]['dir'];
            $builder->orderBy($column, $dir);
        } else {
            $builder->orderBy('id', 'DESC');
        }

        // Handle pagination
        $length = $this->request->getPost('length');
        $start = $this->request->getPost('start');
        if ($length !== -1) {
            $builder->limit($length, $start);
        }

        // Get total records count without filters
        $totalFilteredRecords = $builder->countAllResults(false);

        // Execute the query
        $query = $builder->get();
        $result = $query->getResultArray();

        $data = [];

        foreach ($result as $row) {
            // Check status
            $status = $row['status'] == 1 ? "checked" : "";

            // User profile [The code below uses ternary operator] Example $varaible = 'check' ? 'return true' : 'return false'
            $profile = empty($row['profile']) ? '<img src="' . base_url('public/assets/img/man.png') . '" alt="" class="tableProfile" />' : '<img src="' . base_url('public/profiles/' . $row['profile']) . '" alt="" class="tableProfile" />';

            // Columns to be returned back in DataTable
            $sub_array = [
                '<input type="checkbox" class="user_checkbox checkbox2" data-user-id="' . $row['id'] . '">',
                $profile,
                ucfirst($row['firstname']),
                ucfirst($row['lastname']),
                $row['mobile'],
                $row['email'],
                $row['address'],
                '<div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="slide" value="' . $row['id'] . '" ' . $status . '/>
                </div>',
                '<div class="dropdown">
                    <a class="btn btn-light hidden-arrow dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-danger"></i>
                    </a>
                        <ul class="dropdown-menu">
                            <li><button type="button" id="viewbtn" class="dropdown-item" value="' . $row['id'] . '">
                                <i class="bi bi-eye" style="color:blue"></i> View</button>
                            </li>
                            <li>
                                <button type="button" id="updatebtn" class="dropdown-item" value="' . $row['id'] . '">
                                    <i class="bi bi-pencil" style="color:green"></i> Edit
                                </button>
                            </li>
                            <li>
                                <button type="button" id="deletebtn" class="dropdown-item" value="' . $row['id'] . '">
                                    <i class="bi bi-trash" style="color:red"></i> Delete
                                </button>
                            </li>
                        </ul>
                </div>',
            ];

            $data[] = $sub_array;
        }

        $output = [
            "draw" => intval($this->request->getPost('draw')),
            "recordsTotal" => $totalFilteredRecords, // Count all results without filters
            "recordsFiltered" => $totalFilteredRecords,
            "data" => $data,
        ];

        return $this->response->setJSON($output);
    }


    // Fetch single user
    public function fetchuser()
    {
        $id = $this->request->getPost('userid');

        $user = $this->user->find($id);

        if ($user) {
            return $this->response->setJSON($user);
        } else {
            // Prepare sweet alert response data
            $data = [
                'status' => 'error',
                'message' => 'User not found'
            ];

            return $this->response->setJSON($data);
        }
    }


    // Update user 
    public function updateuser()
    {
        $id = $this->request->getPost('userid');
        $user = $this->user->find($id);

        if (!$user) {
            // Prepare sweet alert response data
            $data = [
                'status' => 'error',
                'message' => 'User not found'
            ];

            return $this->response->setJSON($data);
        }

        // Handle profile image upload and update
        $newName = $this->handleProfileImageUpdate($user['profile']);

        // Check to see if the password field is not empty then update value else keep old password
        if(!empty($this->request->getVar('upassword'))){
            $password = password_hash($this->request->getVar('upassword'), PASSWORD_DEFAULT);
            $this->user->update($id, ['password' => $password,]);
        }

        $data = [
            'firstname' => $this->request->getPost('ufirstname'),
            'lastname' => $this->request->getPost('ulastname'),
            'mobile' => $this->request->getPost('umobile'),
            'email' => $this->request->getPost('uemail'),
            'address' => $this->request->getPost('uaddress'),
            'profile' => $newName
        ];

        $this->user->update($id, $data);

        // Prepare sweet alert response data
        $data = [
            'status' => 'success',
            'message' => 'User updated successfully'
        ];

        return $this->response->setJSON($data);
    }

    // Delete user
    public function deleteuser()
    {
        $id = $this->request->getPost('userid');
        $user = $this->user->find($id);

        if (!$user) {
            // Prepare sweet alert response data
            $data = [
                'status' => 'error',
                'message' => 'User not found'
            ];

            return $this->response->setJSON($data);
        }

        // Delete the user's profile image if it exists
        if ($user['profile'] != null) {
            unlink(FCPATH . 'public/profiles/' . $user['profile']);
        }

        $this->user->delete($id);
        // Prepare sweet alert response data
        $data = [
            'status' => 'success',
            'message' => 'User deleted successfully'
        ];

        return $this->response->setJSON($data);
    }

    // Delete users
    public function deleteUsers()
    {
        // Check if the 'userid' is posted
        $userId = $this->request->getVar('userid');

        if ($userId) {
            $userIds = explode(',', $userId);

            // Fetch user records based on the selected user IDs
            $users = $this->user->whereIn('id', $userIds)->findAll();

            // Loop through users to delete files and user records
            foreach ($users as $user) {
                if (!empty($user['profile'])) {
                    $profilePath = FCPATH . 'public/profiles/' . $user['profile'];
                    if (file_exists($profilePath)) {
                        unlink($profilePath);
                    }
                }
            }

            // Delete user records based on the selected user IDs
            $this->user->whereIn('id', $userIds)->delete();
        }
    }

    // Import users from csv file
    public function importCSVFile()
    {
        $file = $this->request->getFile('csvfile');

        if (!$file->isValid() || $file->hasMoved()) {
            // Handle invalid or moved file
            $data = [
                'status' => 'error',
                'message' => 'Failed to import CSV file'
            ];

            return $this->response->setJSON($data);
        }

        // Generate a random file name
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);

        // Read the uploaded CSV file
        $file = fopen(WRITEPATH . 'uploads/' . $newName, 'r');
        $i = 0;
        $numberOfFields = 5; // 5 is the number of column count in csv file ie id, firstname, etc
        $importData_arr = [];

        // Initialize importData_arr Array
        while (($filedata = fgetcsv($file, 1000, ",")) !== false) {
            $num = count($filedata);

            // Skip the first row & check the number of fields
            if ($i > 0 && $num == $numberOfFields) {
                $importData_arr[$i]['firstname'] = $filedata[0];
                $importData_arr[$i]['lastname'] = $filedata[1];
                $importData_arr[$i]['mobile'] = $filedata[2];
                $importData_arr[$i]['email'] = $filedata[3];
                $importData_arr[$i]['address'] = $filedata[4];
            }
            $i++;
        }
        fclose($file);

        $count = 0;
        foreach ($importData_arr as $userdata) {
            $checkrecord = $this->user->where('email', $userdata['email'])->countAllResults();
            if ($checkrecord == 0) {
                // Insert records
                if ($this->user->save($userdata)) {
                    $count++;
                }
            }
        }

        // Prepare sweet alert response data
        $data = [
            'status' => 'success',
            'message' => 'Users imported successfully'
        ];

        return $this->response->setJSON($data);
    }


    // Working with the status switch Or slider
    public function slider()
    {
        $id = $this->request->getPost('userid');
        $userdata = $this->user->find($id);

        // Check if user's status is true then update it to false and vice versa
        if ($userdata['status'] == 1) {
            // Turn off
            $this->user->update($id, ["status" => '0']);
            $data = ['status' => 'success', 'message' => 'User Deactivated successfully'];
        } else {
            // Turn on
            $this->user->update($id, ["status" => '1']);
            $data = ['status' => 'success', 'message' => 'User Activated successfully'];
        }

        return $this->response->setJSON($data);
    }


    // Check if email exists
    public function checkemail()
    {
        $email = $this->request->getPost('email');

        // Check if the email exists in the users table
        $existingEmail = $this->user->where('email', $email)->first();

        if ($existingEmail) {
            echo "false";
        } else {
            echo "true";
        }
    }

    // Check if mobile exists
    public function checkemobile()
    {
        $mobile = $this->request->getPost('mobile');

        // Check if the email exists in the users table
        $existingMobile = $this->user->where('mobile', $mobile)->first();

        if ($existingMobile) {
            echo "false";
        } else {
            echo "true";
        }
    }

    // Real time Check if email exists
    public function checkemailexists()
    {
        $id = $this->request->getPost('id');
        $email = $this->request->getPost('uemail');

        // Check if the email exists in the database for other users
        $count = $this->user->where('id !=', $id)
            ->where('email', $email)
            ->countAllResults();

        if ($count < 1) {
            echo "true";
        } else {
            echo "false";
        }
    }

    // Real time Check if mobile exists
    public function checkemobileexists()
    {
        $id = $this->request->getPost('id');
        $mobile = $this->request->getPost('umobile');

        // Check if the email exists in the database for other users
        $count = $this->user->where('id !=', $id)
            ->where('mobile', $mobile)
            ->countAllResults();

        if ($count < 1) {
            echo "true";
        } else {
            echo "false";
        }
    }

    // Handle image upload
    private function handleProfileImageUpload()
    {
        $profileImage = null;
        $file = $this->request->getFile('profile');

        if ($file->isValid() && !$file->hasMoved()) {
            // Generate a unique name for the file
            $newName = $file->getRandomName();

            // Create profiles directory if not exists
            if (!directoryExists(FCPATH . 'public/profiles')) {
                mkdir('profiles');
            }

            // Move the uploaded file to the writable/uploads directory
            $file->move(FCPATH . 'public/profiles', $newName);

            $profileImage = $newName;
        }

        return $profileImage;
    }

    // Handle image update
    private function handleProfileImageUpdate($currentProfile)
    {
        $file = $this->request->getFile('uprofile');

        // Create profiles directory if not exists
        if (!directoryExists(FCPATH . 'public/profiles')) {
            mkdir('profiles');
        }

        // If the user has selected a new profile image
        if ($file->isValid() && !$file->hasMoved()) {
            // Delete the previous profile image if it exists
            if ($currentProfile != null) {
                unlink(FCPATH . 'public/profiles/' . $currentProfile);
            }

            // Generate a unique name for the file
            $newName = $file->getRandomName();

            // Move the uploaded file to the writable/uploads directory
            $file->move(FCPATH . 'public/profiles', $newName);
        } else {
            // If no new profile image is selected, keep the existing image
            $newName = $currentProfile;
        }

        return $newName;
    }
}
