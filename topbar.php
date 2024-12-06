<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with SweetAlert</title>
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* General styling for the navbar */
        .custom-navbar {
            background-color: #f8f9fa; /* Light gray for contrast */
            border-bottom: 2px solid #dee2e6; /* Subtle bottom border */
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Branding logo and text */
        .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: black; /* Make "MFSS" black */
            font-weight: bold; /* Optional: makes it stand out more */
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 50%; /* Rounded logo */
            margin-right: 10px; /* Space between logo and text */
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 0;
            min-width: 150px;
        }

        .dropdown-item {
            padding: 10px 15px;
            color: #495057;
            font-size: 0.9rem;
            min-width: 110px;
            transition: background-color 0.3s, color 0.3s;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
            color: #007bff;
        }

        /* User account icon and text */
        .nav-link {
            display: flex;
            align-items: center;
            color: black;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-link i {
            margin-right: 5px;
            font-size: 1.2rem;
        }

        .nav-link:hover {
            color: #007bff;
        }

        /* Media query for responsive design */
        @media (max-width: 400px) {
            .navbar-brand span {
                display: none; /* Hide text on smaller screens */
            }

            .dropdown-menu {
                min-width: 100px;
            }

            .dropdown-item {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<div class="custom-navbar">
    <a class="navbar-brand">
        <img src="mcclogo.jpg" alt="Logo">
        MFSS
    </a>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="account_settings" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-user"></i> <?php echo $_SESSION['login_lastname'] ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="account_settings">
                <a class="dropdown-item" href="#" onclick="confirmLogout()">
                    <center><i class="fa fa-power-off"></i> Logout</center>
                </a>
            </div>
        </li>
    </ul>
</div>

<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will be logged out of your account.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, logout!',
 cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "admin/ajax.php?action=logout2";
            }
        });
    }
</script>
<script>
    $('#manage_my_account').click(function(){
        uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own");
    });
</script>
</body>
</html>
