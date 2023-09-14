$(document).ready(function () {

    // fetchusers
    let dataTable = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "stateSave": false, // Remembers data table state
        "order": [],
        "ajax": {
            url: "fetchusers",
            type: "POST",
        },
        "columnDefs": [{
            "orderable": false,
            "targets": [0, 8]
        }],

        dom: "<'row gx-0 pl-0'<'col-sm-12 col-md-3'l>\
                        <'col-sm-12 col-md-6'B>\
                        <'col-sm-12 col-md-3'f>>" +
            "<'row gx-0'<'col-sm-12'tr>>" +
            "<'row gx-0'<'col-sm-12 col-md-5'i>\
                        <'col-sm-12 col-md-7'p>>",
        buttons: [{
            text: 'Bulk <i class="bi bi-trash"></i>',
            titleAttr: 'Bulk delete',
            className: 'btn btn-sm btn-primary',
            attr: {
                id: 'delete_records'
            }

        },
        {
            extend: 'excelHtml5',
            text: 'Excel <i class="bi bi-file-earmark-excel"></i> ',
            titleAttr: 'Export to Excel',
            className: 'btn btn-sm btn-primary',
            exportOptions: {
                columns: [2, 3, 4, 5, 6],
                search: 'applied',
                order: 'applied',
            }
        },
        {
            extend: 'pdfHtml5',
            text: 'PDF <i class="bi bi-file-earmark-pdf"></i> ',
            titleAttr: 'Export to PDF',
            className: 'btn btn-sm btn-primary',
            filename: 'users_pdf',
            exportOptions: {
                columns: [2, 3, 4, 5, 6],
                search: 'applied',
                order: 'applied',
            }
        },
        {
            extend: 'print',
            text: 'Print <i class="bi bi-printer"></i> ',
            titleAttr: 'Print',
            className: 'btn btn-sm btn-primary',
            exportOptions: {
                columns: [2, 3, 4, 5, 6],
                search: 'applied',
                order: 'applied',
            }
        },
        {
            extend: "copyHtml5",
            text: 'Copy <i class="bi bi-file-earmark"></i> ',
            titleAttr: 'Copy',
            className: 'btn btn-sm btn-warning',
            exportOptions: {
                columns: [2, 3, 4, 5, 6]
            }
        },
        ]

    });

    /**
     * Form validation using JQuery validate plugin
     */
    $.validator.addMethod("regex", function (value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    }, "Invalid format.");

    $.validator.addMethod("extension", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, "Invalid file extension.");

    $.validator.addMethod("filesize", function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, "File size must be less than {0} bytes.");



    // Add user validations
    $("#adduserform").validate({
        rules: {
            'firstname': {
                required: true,
                minlength: 3,
                maxlength: 50,
            },
            'lastname': {
                required: true,
                minlength: 3,
                maxlength: 50,
            },

            'password': {
                required: true,
                minlength: 6,
            },
            'confirmpassword': {
                equalTo: '#password'
            },
            'address': {
                required: true,
                minlength: 5,
                maxlength: 100
            },
            'profile': {
                extension: "jpg|png|jpeg",
                filesize: 8 * (1024 * 1024), // 5mb
            },
        },
        messages: {
            'firstname': {
                required: "First name field is required",
                minlength: "Enter a valid name",
                maxlength: "The name entered is too long"

            },
            'lastname': {
                required: "Last name field is required",
                minlength: "Enter valid name",
                maxlength: "The name entered is too long",
            },
            'password': {
                required: "Password field is required",
                minlength: "Password provided is weak",
            },
            'confirmpassword': {
                equalTo: "Password field should match"
            },
            'address': {
                required: "An address field is required",
                minlength: "Provide a valid address",
                maxlength: "Address field should be informative/short"
            },
            'profile': {
                extension: "Only jpg, png, jpeg are allowed",
                filesize: "Image should not execeed 8MB", // 8mb
            },
        }
    })

    // Update user validations
    $("#updateuserform").validate({
        rules: {
            'ufirstname': {
                required: true
            },
            'ulastname': {
                required: true
            },
            'umobile': {
                required: true,
                minlength: 10,
                maxlength: 17,
                regex: /^[0-9\-\(\)\s]+$/,
                remote: {
                    url: "checkemobileexists",
                    method: "POST",
                    data: {
                        'id': function () {
                            return $('#updateuserform :input[name="userid"]').val();
                        }
                    }
                }
            },
            'uaddress': {
                required: true
            },
            'uprofile': {
                extension: "jpg|png|jpeg",
                filesize: 8 * (1024 * 1024), // 5mb
            },
            'upassword': {
                minlength: 6,
            },
            'uemail': {
                required: true,
                email: true,
                remote: {
                    url: "checkemailexists",
                    method: "POST",
                    data: {
                        'id': function () {
                            return $('#updateuserform :input[name="userid"]').val();
                        }
                    }
                }
            }

        },
        messages: {
            'ufirstname': {
                required: "First name field can't be blank"
            },
            'ulastname': {
                required: "Last name field can't be blank"
            },
            'umobile': {
                required: "Contact field can't be blank",
                minlength: "Phone number is wrong",
                maxlength: "Phone number is too long",
                regex: "Enter a valid phone number",
                remote: "Someone else already uses contact"
            },
            'uaddress': {
                required: "Address field can't be blank"
            },
            'uprofile': {
                extension: "Only jpg, png and jpeg allowed",
                filesize: "Profile size maximum is 8MB", // 8mb
            },
            'upassword': {
                minlength: "Field requires 6 or more characters",
            },
            'uemail': {
                remote: "Someone else uses that email",
                emal: "Please enter valid a email"
            }

        }
    })

    // Upload csv file validations
    $("#bulkForm").validate({
        rules: {
            'csvfile': {
                required: true,
                extension: "csv",
            }
        },
        messages: {
            'csvfile': {
                required: 'Upload field can not be empty',
                extension: "Only CSV file formats are allowed",
            }
        }
    })


    // Add image preview
    const image = document.querySelector(".preview");
    const input = document.querySelector("#profile");

    input.addEventListener('change', () => {
        image.src = URL.createObjectURL(input.files[0]);
    });

    // Resetting the image preview on click to Reset the add form modal
    document.querySelector("#popadd").addEventListener('click', function () {
        image.src = "./public/assets/img/man.png";
        $("#adduserform").trigger('reset');
    });

    /**
     * Add user
     */
    $(document).on('submit', '#adduserform', function (event) {
        event.preventDefault();

        $.ajax({
            method: "POST",
            url: "adduser",
            data: new FormData($("#adduserform")[0]),
            processData: false,
            contentType: false,
            success: function (response) {
                $("#usermodalAdd").modal('hide');
                Swal.fire({
                    position: 'center',
                    icon: response.status,
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000 // 2 seconds
                });

                $("#adduserform").trigger('reset');
                dataTable.ajax.reload();
            }
        });
    });



    // Update
    // STEP 1: Fetch user data and populate the update form
    $(document).on('click', '#updatebtn', function (event) {
        event.preventDefault();
        const id = $(this).val();

        $.ajax({
            method: 'POST',
            url: 'fetchuser',
            data: { userid: id },
            success: function (response) {
                populateUpdateForm(response, id);
            }
        });
    });

    function populateUpdateForm(userData, userId) {
        $('#ufirstname').val(userData.firstname);
        $('#ulastname').val(userData.lastname);
        $('#umobile').val(userData.mobile);
        $('#uemail').val(userData.email);
        $('#uaddress').val(userData.address);
        $('#userid').val(userId);

        const img = document.querySelector('#updateimg');
        img.src = userData.profile ? './public/profiles/' + userData.profile : './public/assets/img/man.png';

        $('#updateUserModal').modal('show');
    }

    // STEP 2: Submit the updated user data
    $(document).on('submit', '#updateuserform', function (event) {
        event.preventDefault();

        $.ajax({
            method: 'POST',
            url: 'updateuser',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (response) {
                handleUpdateResponse(response);
            }
        });
    });

    function handleUpdateResponse(response) {
        $('#updateUserModal').modal('hide');

        Swal.fire({
            position: 'center',
            icon: response.status,
            title: response.message,
            showConfirmButton: false,
            timer: 2000 // 2 seconds
        });

        $('#updateuserform').trigger('reset');
        dataTable.ajax.reload();
    }


    // Delete user
    $(document).on('click', '#deletebtn', function (event) {
        event.preventDefault();
        const id = $(this).val();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                deleteUser(id);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire('Cancelled', 'User data is safe :)', 'error');
            }
        });
    });

    function deleteUser(userId) {
        $.ajax({
            method: 'POST',
            url: 'deleteuser',
            data: { userid: userId },
            success: function (response) {
                handleDeleteResponse(response);
            }
        });
    }

    function handleDeleteResponse(response) {
        Swal.fire({
            title: response.status === 'success' ? 'Deleted!' : 'Error!',
            text: response.message,
            icon: response.status,
        });

        if (response.status === 'success') {
            dataTable.ajax.reload();
        }
    }


    // Delete Records | Select All Checkboxes
    $(document).on('click', '#select_all', function () {
        $(".user_checkbox").prop("checked", this.checked);
        $("#select_count").html($("input.user_checkbox:checked").length + " Selected");
    });

    // Individual Checkbox Click
    $(document).on('click', '.user_checkbox', function () {
        if ($('.user_checkbox:checked').length == $('.user_checkbox').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });

    // Delete Selected Records
    $(document).on('click', '#delete_records', function () {
        var selectedUsers = [];
        $(".user_checkbox:checked").each(function () {
            selectedUsers.push($(this).data('user-id'));
        });

        if (selectedUsers.length <= 0) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'No record(s) selected',
                showConfirmButton: false,
                timer: 2000 // 2 seconds
            });
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-outline-success m-2',
                    cancelButton: 'btn m-2'
                },
            });

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedValues = selectedUsers.join(",");
                    $.ajax({
                        type: "POST",
                        url: "deleteusers",
                        cache: false,
                        data: { userid: selectedValues },
                        success: function (data) {
                            document.getElementById("select_all").checked = false;
                            dataTable.ajax.reload();
                        }
                    });
                    swalWithBootstrapButtons.fire(
                        'Deleted!',
                        selectedUsers.length + ' user(s) deleted successfully.',
                        'success'
                    );
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Operation cancelled :)',
                        'error'
                    );
                }
            });
        }
    });



    // View user modal
    $(document).on('click', '#viewbtn', function (event) {
        event.preventDefault();
        const id = $(this).val();

        $.ajax({
            method: 'POST',
            url: 'fetchuser',
            data: { userid: id },
            success: function (response) {
                populateViewModal(response);
            }
        });

        $("#viewModal").modal('show');
    });

    function populateViewModal(user) {
        $('#viewuser').html(user.firstname + ' ' + user.lastname);
        $('#viewemail').html(user.email);
        $('#viewmobile').html(user.mobile);
        $('#viewlocation').html(user.address);

        if (user.profile != null) {
            $('#viewprofile').attr('src', './public/profiles/' + user.profile);
        } else {
            $('#viewprofile').attr('src', './public/assets/img/man.png');
        }
    }


    // Import users from a csv file
    $(document).on('submit', '#bulkForm', function (event) {

        event.preventDefault();
        $.ajax({
            method: "POST",
            url: "csv",
            data: new FormData($("#bulkForm")[0]),
            processData: false,
            contentType: false,
            success: function (response) {
                $("#uploadModal").modal('hide');
                Swal.fire({
                    position: 'center',
                    icon: response.status,
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000 // 2 seconds
                });

                $("#bulkForm").trigger('reset');

                dataTable.ajax.reload();
            }
        });
    });


    // Bootstrap switch button
    $(document).on('click', '#slide', function () {

        let id = $(this).val();

        $.ajax({
            method: "POST",
            url: "slider",
            data: {
                userid: id
            },
            success: function (response) {
                Swal.fire({
                    position: 'center',
                    icon: response.status,
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000 // 2 seconds
                });

                dataTable.ajax.reload()
            }
        });

    });



})


// Real time email validation
$(document).ready(function () {
    $("#email").rules("add", {
        remote: {
            url: "checkemail",
            type: "POST",

        },
        required: true,
        messages: {
            remote: "Email already in use",
            required: "Email field is required",
        }
    })


});

// Real time mobile validation
$(document).ready(function () {
    $("#mobile").rules("add", {
        remote: {
            url: "checkemobile",
            type: "POST",
        },
        required: true,
        minlength: 10,
        maxlength: 17,
        regex: /^[0-9\-\(\)\s]+$/,
        messages: {
            remote: "Contact already in use",
            required: "Contact field is required",
            minlength: "Provide a valid contact",
            maxlength: "Provided number is too long",
            regex: "Example +256 or (256) or 07--",
        }
    })


});



