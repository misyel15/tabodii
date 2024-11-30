<?php
session_start();
include("db_connect.php");
include 'includes/style.php'; 
include 'includes/head.php'; 

// Ensure that the token is retrieved from the URL or session
if (isset($_GET['token'])) {         
    $_SESSION['token'] = $_GET['token'];
} elseif (!isset($_SESSION['token'])) {
    echo '<script>
        window.onload = function() {
            Swal.fire({
                title: "Error!",
                text: "Invalid or expired token. Please request a new password reset.",
                icon: "error"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "forgot.php"; // Redirect to password reset request page
                }
            });
        };
      </script>';
    exit();
}

$token = $_SESSION['token'];

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo '<script>
            window.onload = function() {
                Swal.fire({
                    title: "Error!",
                    text: "Passwords do not match.",
                    icon: "error"
                });
            };
          </script>';
    } else {
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Use prepared statements to prevent SQL injection
        $stmt_update = $conn->prepare("UPDATE users SET password=? WHERE reset_token=?");
        if ($stmt_update === false) {
            error_log('MySQL prepare error: ' . $conn->error); // Log prepare error
            echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to prepare the statement.",
                        icon: "error"
                    });
                };
              </script>';
            exit();
        }

        $stmt_update->bind_param("ss", $hashed_password, $token);

        if ($stmt_update->execute()) {
            // Clear the token after successful password update
            unset($_SESSION['token']);
            echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Password successfully changed!",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "index.php";
                        }
                    });
                };
              </script>';
        } else {
            error_log('MySQL execute error: ' . $stmt_update->error); // Log execution error
            echo '<script>
                window.onload = function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Error changing the password.",
                        icon: "error"
                    });
                };
              </script>';
        }

        $stmt_update->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Forgot Password</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    #logo-img {
        width: 5em;
        height: 5em;
        object-fit: scale-down;
        object-position: center center;
    }
    .login-box {
        max-width: 360px;
        margin: 0 auto;
        margin-top: 10% ;
    }
    .card {
        border-radius: 10px;
    }
    .btn-primary {
        background-color: #9900ff;
        border-radius: 10px;
    }
    .input-group-append button {
        border-radius: 0 5px 5px 0;
    }
    .toggle-icon {
        cursor: pointer;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <img src="assets/uploads/mcclogo.jpg" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img">
      <a class="h2"><b>Reset Password</b></a>
    </div>
    <div class="card-body">
      <form action="reset_password.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <div class="input-group mb-3">
            <input type="password" class="form-control" autocomplete="off" name="password" id="password" placeholder="New Password" required>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="oldPasswordToggle">
                    <i class="fas fa-eye toggle-icon"></i>
                </button>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control" autocomplete="off" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="confirmPasswordToggle">
                    <i class="fas fa-eye toggle-icon"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" name="submit" class="btn btn-primary btn-block">Submit</button>
            </div>
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<script>
document.getElementById('oldPasswordToggle').addEventListener('click', function() {
    var passwordField = document.getElementById('password');
    var toggleIcon = this.querySelector('.toggle-icon');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
});

document.getElementById('confirmPasswordToggle').addEventListener('click', function() {
    var passwordField = document.getElementById('confirm_password');
    var toggleIcon = this.querySelector('.toggle-icon');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
});
</script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
