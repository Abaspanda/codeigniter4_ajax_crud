<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX CRUD</title>
    <!-- ========== Start Vendor CSS ========== -->
    <?= link_tag('public/assets/vendor/bootstrap/css/bootstrap.min.css') ?>
    <?= link_tag('public/assets/vendor/sweetalert/css/sweetalert.min.css') ?>
    <?= link_tag('public/assets/vendor/datatables/datatables.min.css') ?>
    <?= link_tag('public/assets/vendor/datatables/responsive.bootstrap5.min.css') ?>
    <?= link_tag('public/assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>
    <?= link_tag('public/assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>
    <?= link_tag('public/assets/css/styles.css') ?>

</head>

<body>
    <!-- Buttons For Adding User and Bulk Import users from CSV file -->
    <div class="container mt-4 mb-4">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <bi class="bi-card-checklist"></bi>
                Bulk Import
            </button>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#usermodalAdd" id="popadd">
                <bi class="bi-plus"></bi>
                Add User
            </button>
        </div>
    </div>

    <!-- Add User Modal -->

    <!-- Modal Body -->
    <div class="modal fade" id="usermodalAdd" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" id="adduserform">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="public/assets/img/man.png" alt="" class="updateimg preview" id="addimg">
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">First name</label>
                                    <input type="text" class="form-control form-control-sm" name="firstname" id="firstname" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Last name</label>
                                    <input type="text" class="form-control form-control-sm" name="lastname" id="lastname" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">Contact</label>
                                    <input type="text" class="form-control form-control-sm" name="mobile" id="mobile" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-sm" name="email" id="email" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control form-control-sm" name="password" id="password" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="confirmpassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control form-control-sm" name="confirmpassword" id="confirmpassword" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control form-control-sm" name="address" id="address" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profile" class="form-label">Profile</label>
                                    <input type="file" class="form-control form-control-sm" name="profile" id="profile" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-sm btn-primary" value="Register User" name="saveuser" id="saveuser">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update User Modal -->

    <!-- Modal Body -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" id="updateuserform">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="" alt="" class="updateimg" id="updateimg">
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ufirstname" class="form-label">First name</label>
                                    <input type="text" class="form-control form-control-sm" name="ufirstname" id="ufirstname" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ulastname" class="form-label">Last name</label>
                                    <input type="text" class="form-control form-control-sm" name="ulastname" id="ulastname" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="umobile" class="form-label">Contact</label>
                                    <input type="text" class="form-control form-control-sm" name="umobile" id="umobile" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="uemail" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-sm" name="uemail" id="uemail" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="uaddress" class="form-label">Address</label>
                                    <input type="text" class="form-control form-control-sm" name="uaddress" id="uaddress" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="upassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control form-control-sm" name="upassword" id="upassword" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="uprofile" class="form-label">Profile</label>
                                    <input type="file" class="form-control form-control-sm" name="uprofile" id="uprofile" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="userid" name="userid">
                        <input type="submit" class="btn btn-sm btn-primary" value="Update User" name="updateuser" id="updateuser">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">User Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-light">
                        <div class="blue"></div>
                        <div class="white"></div>
                        <div class="cover p-3">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-md-5">
                                    <img src="" class="rounded-circle" alt="" id="viewprofile">
                                </div>
                            </div>
                            <h3 class="heading text-center" id="viewuser"></h3>
                            <div class="container">
                                <div class="mb-3">
                                    <i class="bi bi-envelope"></i>&nbsp;
                                    <span><b id="viewemail"></b></span>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-phone"></i>&nbsp;
                                    <span><b id="viewmobile"></b></span>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-map"></i>&nbsp;
                                    <span><b id="viewlocation"></b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload CSV file -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" id="bulkForm">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Import Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <input type="file" name="csvfile" id="csvfile" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" value="Upload" class="btn btn-sm btn-primary" id="uploadfile">
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="container">
        <table id="userTable" class="table table-bordered table-hover table-striped responsive">
            <thead style="background-color: #635f5f;color:#fff">
                <tr>
                    <th><input type="checkbox" id="select_all" class="checkbox"></th>
                    <th>Profile</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- ========== Start Vendor JS ========== -->
    <?= script_tag('public/assets/vendor/jquery-3.6.1.min.js') ?>
    <?= script_tag('public/assets/vendor/jquery.validate.min.js') ?>
    <?= script_tag('public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>
    <?= script_tag('public/assets/vendor/sweetalert/js/sweetalert2@11.js') ?>
    <?= script_tag('public/assets/vendor/datatables/datatables.min.js') ?>
    <?= script_tag('public/assets/vendor/datatables/dataTables.responsive.min.js') ?>
    <?= script_tag('public/assets/vendor/datatables/pdfmake.min.js') ?>
    <?= script_tag('public/assets/vendor/datatables/responsive.bootstrap5.min.js') ?>
    <?= script_tag('public/assets/vendor/datatables/vfs_fonts.js') ?>
    <?= script_tag('public/assets/js/main.js') ?>
</body>

</html>