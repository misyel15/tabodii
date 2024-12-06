<?php
session_start();

include("db_connect.php");
include 'includes/style.php'; 
include 'includes/head.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Set timezone
date_default_timezone_set('Asia/Manila'); // Changed to 'Asia/Manila' for Philippines

function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zeninmacky05@gmail.com'; // SMTP username
        $mail->Password = 'frut mage zsxu mzsd';    // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('mccschedsystem@gmail.com', 'MCC SCHED SYSTEM ADMIN');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body = "
        <html>
        <body>
            <p>Dear User,</p>
            <p>Your OTP for password reset is: <strong>$otp</strong></p>
            <p>This OTP is valid for 30 minutes. Do not share it with anyone.</p>
            <p>Regards,<br>Your App Team</p>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

$step = "request_otp";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_otp'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $check = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check);

        if ($result && mysqli_num_rows($result) == 1) {
            $otp = rand(100000, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime('+30 minutes')); // Increased to 30 minutes

            $update = "UPDATE users SET otp = '$otp', otp_expiry = '$expiry' WHERE email = '$email'";

            if (mysqli_query($conn, $update)) {
                if (sendOTP($email, $otp)) {
                    $step = "verify_otp";
                    $_SESSION['email'] = $email;
                    $message = "OTP sent to your email. Please check your inbox.";
                } else {
                    $message = "Failed to send OTP via email. Please try again.";
                }
            } else {
                $message = "Failed to update OTP in the database: " . mysqli_error($conn);
            }
        } else {
            $message = "No account associated with this email.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $email = $_SESSION['email'] ?? null;
        $otp = mysqli_real_escape_string($conn, $_POST['otp']);

        $check = "SELECT * FROM users WHERE email = '$email' AND otp = '$otp' AND otp_expiry > NOW()";
        $result = mysqli_query($conn, $check);

        if ($result && mysqli_num_rows($result) == 1) {
            $step = "reset_password";
            $message = "OTP verified. Please reset your password.";
        } else {
            $message = "Invalid OTP or OTP expired.";
        }
    } elseif (isset($_POST['reset_password'])) {
        $email = $_SESSION['email'] ?? null;
        $new_password = $_POST['new_password'];
        
        if (strlen($new_password) < 8) {
            $message = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]/', $new_password) || ! preg_match('/[0-9]/', $new_password)) {
            $message = "Password must contain at least one uppercase letter and one number.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update = "UPDATE users SET password = '$hashed_password', otp = NULL, otp_expiry = NULL WHERE email = '$email'";

            if (mysqli_query($conn, $update)) {
                $message = "Password reset successfully. You can now log in.";
                unset($_SESSION['email']);
                $step = "request_otp";
            } else {
                $message = "Failed to reset password. Please try again later.";
            }
        }
    } elseif (isset($_POST['resend_otp'])) {
        $email = $_SESSION['email'] ?? null;
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+30 minutes')); // Increased to 30 minutes

        $update = "UPDATE users SET otp = '$otp', otp_expiry = '$expiry' WHERE email = '$email'";

        if (mysqli_query($conn, $update)) {
            if (sendOTP($email, $otp)) {
                $message = "OTP resent to your email.";
            } else {
                $message = "Failed to resend OTP. Please try again.";
            }
        } else {
            $message = "Failed to update OTP in the database: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Mcc Faculty Scheduling</title>
    <link rel="icon" href="assets/uploads/mcclogo.jpg" type="image/png">

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    
    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <style>
        /* Main layout adjustments */
        body {
            background-color: #f4f4f4;
            font-family: 'Source Sans Pro', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: lightgray;
            color: black;
            text-align: center;
            padding: 1.5rem;
            border-radius: 20px 20px 0 0;
        }

        .h1 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .card-body {
            padding: 2rem;
        }

        .input-group-text {
            background-color: #f4f4f4;
        }

        .btn {
            background-color: #007bff;
            border: none;
        }

        /* Logo styling */
        #logo-img {
            width: 5em;
            height: 5em;
            object-fit: cover;
            object-position: center center;
            border-radius: 50%;
        }

        /* Make the layout responsive */
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .h1 {
                font-size: 1.5rem;
            }

            #logo-img {
                width: 4em;
                height: 4em;
            }

            .btn {
                padding: 0.75rem 1rem;
            }

            .login-box {
                margin: 10px;
            }
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <center><img src="assets/uploads/mcclogo.jpg" alt="System Logo" class="img-thumbnail rounded-circle" id="logo-img"></center>
            <a class="h1"><b>Retrieve</b>|Account</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            <!-- SweetAlert Message -->
            <?php if ($message): ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>
                <script>
                    Swal.fire({
                        title: 'Notification',
                        text: '<?= htmlspecialchars($message) ?>',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                </script>
            <?php endif; ?>

            <?php if ($step === "request_otp"): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <button type="submit" name="request_otp" class="btn btn-primary w-100">Send OTP</button>
                </form>
            <?php elseif ($step === "verify_otp"): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" name="otp" class="form-control" id="otp" required>
                    </div>
                    <button type="submit" name="verify_otp" class="btn btn-primary w-100">Verify OTP</button>
                    <button type="submit" name="resend_otp" class="btn btn-secondary w-100 mt-2">Resend OTP</button>
                </form>
            <?php elseif ($step === "reset_password"): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" id="new_password" required>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button>
                </form>
            <?php endif; ?>

                 <p class="mt-3 mb-1">
                <a href="index.php">Login</a>
            </p>
        </div>
    </div>
</div>

<!-- SweetAlert JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>

</body>

</html>
