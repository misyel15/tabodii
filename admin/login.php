<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize session variables for login attempts if not already set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = 0;
    }

    $max_attempts = 3; // Maximum allowed failed login attempts
    $lockout_duration = 1 * 60; // Lockout duration in seconds (5 minutes)

    // Check if the user is currently locked out
    if ($_SESSION['login_attempts'] >= $max_attempts && time() < $_SESSION['lockout_time']) {
        $remaining_time = $_SESSION['lockout_time'] - time();
        echo json_encode([
            'status' => 'locked',
            'message' => 'Too many failed attempts. Please try again in ' . ceil($remaining_time / 60) . ' minutes.'
        ]);
        exit;
    }

    // Sanitize and retrieve user inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $course = htmlspecialchars(trim($_POST['course']));

    // Capture geolocation data
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;

    // CAPTCHA verification (your existing CAPTCHA code here)
    $captcha_response = $_POST['h-captcha-response'];
    $secret_key = 'ES_7f358ad256b1474aa1262e98acc952ae';
    $captcha_verify = file_get_contents("https://hcaptcha.com/siteverify?secret=$secret_key&response=$captcha_response");
    $captcha_response_data = json_decode($captcha_verify);
    if (!$captcha_response_data->success) {
        echo 5;
        exit;
    }


    // Prepare and execute login query
    $stmt = $conn->prepare("
    SELECT id, name, username, course, dept_id, type, password 
    FROM users 
    WHERE username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $stored_hashed_password = $user_data['password'];

        // Verify the password using password_verify
        if (password_verify($password, $stored_hashed_password)) {
            // Verify course matches
            if ($user_data['course'] === $course) {
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lockout_time'] = 0;

                // Store user session data
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['dept_id'] = $user_data['dept_id'];
                $_SESSION['username'] = htmlspecialchars($user_data['username']);
                $_SESSION['name'] = htmlspecialchars($user_data['name']);
                $_SESSION['login_type'] = $user_data['type'];

                // Optionally, store geolocation data
                $_SESSION['latitude'] = $latitude;
                $_SESSION['longitude'] = $longitude;

                // Check if the user is an admin (user type != 1)
                if ($_SESSION['login_type'] != 1) {
                    session_unset(); // Log out if not an admin
                    echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
                } else {
                    echo json_encode(['status' => 'success', 'message' => 'Login Successful']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Course Mismatch']);
            }
        } else {
            // Increment login attempts and handle lockout
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                $_SESSION['lockout_time'] = time() + $lockout_duration;
            }
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="School Faculty Scheduling System">
    <meta name="author" content="Your Name">
    <meta name="keywords" content="School, Faculty, Scheduling, System">

    <!-- Title Page-->
    <title>Login</title>
    <link rel="icon" href="assets/uploads/mcclogo.jpg" type="image/jpg">
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

    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    
          <!-- reCAPTCHA Widget -->
      <script src="https://hcaptcha.com/1/api.js" async defer></script>

   
</head>

<style>


    body.animsition {
        background-color: #f0f2f5; /* Light gray background color */
    }

    .page-wrapper {
        background-color: #eae6f5; /* White background for the page wrapper */
        padding-top: 50px; /* Add some spacing at the top */
    }

    .login-wrap {
        background-color: #ffffff; /* White background for the login card */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow for the card */
    }

    .login-content {
        background-color: #ffffff; /* Background for content */
    }

    .password-container {
        position: relative;
        width: 100%;
    }

    .au-input {
        width: 100%;
        padding-right: 40px; /* Adjust to make space for the icon */
    }

    .eye-icon {
        position: absolute;
        right: 10px; /* Adjust according to your design */
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }
 
    .form-group .g-recaptcha {
        transform: scale(0.85); /* Adjust scale to fit */
        transform-origin: 0 0; /* Set origin to top-left */
    }

    @media (max-width: 600px) {
        .form-group .g-recaptcha {
            transform: scale(0.75); /* Smaller for smaller screens */
            transform-origin: 0 0;
        }
    }

</style>

<body class="animsition">
    <div class="page-wrapper">
        <div class="page-content--bge4">
            <div class="container">
                <div class="login-wrap" style="margin-top:0%; max-width: 450px; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #fff;">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                                <img src="assets/uploads/mcclogo.jpg" style="width:150px; height:90px;" alt="CoolAdmin">
                            </a>
                            <h3> Welcome Admin</h3>
                        </div>
                        <div class="login-form">
                            <form id="login-form">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input class="au-input au-input--full" type="email" name="username" placeholder="Username" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <div class="password-container">
                                        <input class="au-input au-input--full" type="password" id="password" name="password" placeholder="Password" required>
                                        <i class="fas fa-eye-slash eye-icon" id="togglePassword"></i>
                                    </div>
                                </div>
                                <!-- Course Field -->
                                <div class="form-group">
                                    <label>Course</label>
                                    <div class="col-sm-13">
                                        <select class="form-control" name="course" id="course" required>
                                            <option value="" disabled selected>Select Course</option>
                                            <option value="BSIT">BSIT</option>
                                            <option value="BSBA">BSBA</option>
                                            <option value="BSHM">BSHM</option>
                                            <option value="BSED">BSED</option>
                                        </select>
                                    </div>
                                </div>
 
                        
                          <!-- Updated HTML for hCaptcha -->
                          <div class="form-group">
                      <div class="h-captcha" data-sitekey="0a809f3c-8a90-4672-9d9a-0508be54f062"></div> <!-- Replace with your actual site key -->
                       </div>
<!-- Terms and Conditions Checkbox -->
<div class="form-group">
    <input type="checkbox" id="terms" name="terms" required>
    <label for="terms">I agree to the 
        <a href="#" data-toggle="modal" data-target="#termsModal">Terms and Conditions</a>
    </label>
</div>

<!-- Modal Structure for Terms and Conditions -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Terms and Conditions for MCC Faculty Scheduling System</h4>
                
                <h5>1. System Usage</h5>
                <p>Faculty members and administrators must use the system solely for scheduling, faculty loading, and room management purposes. Unauthorized or unrelated activities are strictly prohibited.</p>

                <h5>2. Data Accuracy</h5>
                <p>Users are responsible for ensuring the accuracy of data entered into the system, including class schedules, faculty loads, and room assignments. Incorrect or incomplete data may result in system errors or schedule conflicts.</p>

                <h5>3. Privacy and Confidentiality</h5>
                <p>Personal information such as faculty details and class schedules must be kept confidential. Users must protect login credentials and avoid sharing them with others.</p>

                <h5>4. System Integrity</h5>
                <p>Users must not engage in activities that may compromise system security, such as hacking, data tampering, or introducing malicious software.</p>

                <h5>5. Accessibility and Maintenance</h5>
                <p>The system may be temporarily unavailable due to maintenance, upgrades, or unforeseen technical issues. MCC will make reasonable efforts to restore access promptly.</p>

                <h5>6. Policy Compliance</h5>
                <p>All users must comply with the policies set by the institution regarding faculty scheduling, room usage, and system access. Non-compliance may lead to account suspension or administrative action.</p>

                <h5>7. Modifications and Updates</h5>
                <p>MCC reserves the right to update these terms and conditions as needed. Continued use of the system after updates indicates acceptance of the revised terms.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                                <button class="au-btn au-btn--block au-btn--blue m-b-20" type="submit">Login</button>
                                <a href="https://mccfacultyscheduling.com/login" class="au-btn au-btn--block au-btn--green m-b-20" style="text-align:center;">Home</a>
                                  <center>  
                                            <a href="form" class="forgot-password-btn">Forgot Password?</a>
                                       
                                    </center> 
       

                            </form>
                 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // Handle form submission
        $(document).ready(function() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        // Get geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Serialize form data with geolocation
                const formData = $('#login-form').serialize() + '&latitude=' + latitude + '&longitude=' + longitude;

                // Disable button and show loading
                $('#login-form button[type="submit"]').attr('disabled', true).html('Logging in...');

                // Submit form data via AJAX
                $.ajax({
                    type: 'POST',
                    url: 'login.php',
                    data: formData,
                    success: function(resp) {
                        const response = JSON.parse(resp);

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: response.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href = 'home';
                            });
                        } else if (response.status === 'locked') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Too Many Attempts',
                                text: response.message
                            });
                        } else if (resp == 5) {
                            Swal.fire({
                                icon: 'error',
                                title: 'CAPTCHA Failed',
                                text: 'Please complete the CAPTCHA.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: response.message
                            });
                        }

                        // Re-enable button
                        $('#login-form button[type="submit"]').removeAttr('disabled').html('Login');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'There was an error processing your request. Please try again.'
                        });

                        // Re-enable button
                        $('#login-form button[type="submit"]').removeAttr('disabled').html('Login');
                    }
                });
            }, function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocation Error',
                    text: 'Unable to retrieve your location. Please check your browser settings.'
                });
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Geolocation Not Supported',
                text: 'Your browser does not support geolocation.'
            });
        }
    });
});
$('#terms-link').on('click', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Terms and Conditions',
        html: 'Your detailed terms and conditions go here...',
        icon: 'info',
        confirmButtonText: 'Close'
    });
});
        
    </script>
</body>
</html>
