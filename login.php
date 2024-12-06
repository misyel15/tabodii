<?php 
include 'style.php'; 
include 'headers.php'; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Basic Meta Tags -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  
  <!-- External Libraries and Stylesheets -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />
  <title>MCC Faculty Scheduling</title>
  <link rel="icon" type="image/png" href="mcclogo.jpg">
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap1.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Fonts Style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    .cookie-consent-content button {
    background-color: #4caf50; /* Green for Accept */
    border: none;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px; /* Space between buttons */
}

.cookie-consent-content button#declineCookie {
    background-color: #f44336; /* Red for Decline */
}

.cookie-consent-content button:hover {
    opacity: 0.8;
}

  .cookie-consent-banner {
  position: fixed;
  bottom: 10px;
  left: 0%;
  width: 100%;
  background-color: #333;
  color: white;
  padding: 10px;
  text-align: center;
  z-index: 9999;
  display: block;
}

.cookie-consent-banner .cookie-consent-content {
  display: inline-block;
}

.cookie-consent-banner button {
  margin-left: 10px;
  padding: 5px 15px;
  cursor: pointer;
}

    /* Custom Styles */
    .text-gradient {
      background: linear-gradient(315deg, #1e30f3 0%, #6CBAA5 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .dropdown-item:hover {
      background-color: lightgray;
      color: #fff;
    }

    .carousel-item img {
      border-radius: 50%;
      max-height: 380px;
      width: 100%;
      object-fit: cover;
    }

  .header_section {
    background: linear-gradient(315deg, #C8A182 0%, #67574A 100%);

  position: sticky;
  top: 0;
  z-index: 1000; /* Ensures the header stays above other elements */
  width: 100%;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Optional shadow for visual separation */
}


    .navbar-brand img {
      border: 2px solid #333;
      padding: 5px;
      background-color: #f8f9fa;
      border-radius: 50%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease-in-out;
    }

    .navbar-brand img:hover {
      transform: scale(1.1);
    }

    .logo-container {
      display: flex;
      align-items: center;
    }

    .logo-text {
      font-size: 1.0rem;
      color: white;
      margin-left: 10px;
      font-weight: bold;
    }

    .navbar-nav .nav-link,
    .navbar-nav .dropdown-item {
      color: black !important;
    }

    .navbar-nav .nav-link:hover {
      background-color: black;
      color: #fff !important;
      border-radius: 5px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .navbar-nav .dropdown-item:hover {
      background-color: black;
      color: #fff !important;
      border-radius: 50px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-link.button-style {
      background: linear-gradient(to right, #80DFE1, #6CBAA5);

      color: #fff !important;
      border-radius: 50px;
      padding: 10px 20px;
      font-weight: bold;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .nav-link.button-style:hover {
      background-color: gray;
      color: #fff !important;
    }

    /* Footer Calendar Box */
    .calendar-box {
      background-color: #f2f2f7; /* Custom background color */
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .calendar-icon {
      font-size: 50px;
    }

    /* Media Queries for Mobile Navigation */
    @media (max-width: 767px) {
      .navbar-nav {
        flex-direction: column; /* Stack the nav items */
        align-items: center; /* Center the nav items */
        width: 100%; /* Make sure it takes full width */
      }

      .navbar-nav .nav-item {
        margin-left: 0; /* Reset margin for mobile */
        margin-bottom: 10px; /* Add some spacing between items */
        
      }
    }
  </style>
</head>

<body>
<!-- Header Section -->
<header class="header_section">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
            <a class="navbar-brand" href="index.php">
                <div class="logo-container">
                    <img src="mcclogo.jpg" width="50px" height="50px" alt="System Logo" class="img-thumbnail rounded-circle">
                    <span class="logo-text ms-3">MFSS</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto"> <!-- Use me-auto to align items to the left -->
                    <li class="nav-item" style="margin-left:20px;">
                        <a class="nav-link button-style" href="index.php">Home</a>
                    </li>
                    <li class="nav-item" style="margin-left:20px;">
                        <a class="nav-link button-style" href="about.php">About</a>
                    </li>
                    <li class="nav-item dropdown" style="margin-left:20px;">
                        <a class="nav-link dropdown-toggle button-style" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Login
                        </a>
                      <div class="dropdown-menu" aria-labelledby="navbarDropdown " style="margin-left: -60%;">
                      <center>     <a class="dropdown-item" href="home.php"><i class="fas fa-user-secret"></i> Instructor</a>
                            <a class="dropdown-item" href="./admin/login.php"><i class="fas fa-user-cog"></i> Admin</a></center>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

  <!-- Main Content Section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="detail_box">
            <h1 class="display-3 fw-bolder mb-5">
              <marquee style="font-size: 50px;">
                <span class="text-gradient" style="font-family: Algerian;">Welcome to MCC Faculty Scheduling</span>
              </marquee>
            </h1>
            <p class="lead">Here you can manage the scheduling of all faculty members efficiently and effectively. Our system helps you stay organized and up-to-date with the latest changes in the schedule.</p>
          </div>
        </div>
        
        <!-- Image Carousel Section -->
        <div class="col-lg-6 col-md-6">
          <div class="img_content" style="background: transparent; margin-top:30px;">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="3000" data-pause="false">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="end.jpg" class="d-block w-80" alt="Logo Image">
                </div>
                <div class="carousel-item">
                  <img src="end.jpg" class="d-block w-80" alt="End Image">
                </div>
              </div>
              <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<br>
   <!-- Footer Section -->
  <footer class="footer_section">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-4">
          <div class="calendar-box">
            <div class="calendar-icon" style="color: #1e30f3;"><i class="fas fa-calendar-alt"></i></div>
            <h5 class="mt-3">Class Schedules</h5>
            <p>Manage and view all upcoming class schedules.</p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="calendar-box">
            <div class="calendar-icon" style="color: #e21e80;"><i class="fas fa-calendar-check"></i></div>
            <h5 class="mt-3">Faculty Meetings</h5>
            <p>Keep track of faculty meetings and other important events.</p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="calendar-box">
            <div class="calendar-icon" style="color: #28a745;"><i class="fas fa-calendar-plus"></i></div>
            <h5 class="mt-3">Create New Schedules</h5>
            <p>Effortlessly create new schedules for classes and events.</p>
          </div>
        </div>
      </div>
    </div>
    <div id="cookieConsent" class="cookie-consent-banner">
        <div class="cookie-consent-content">
        <p>We use cookies to improve your experience. By using our website, you consent to our use of cookies. <a href="#">Learn more</a></p>
        <button id="acceptCookie">Accept</button>
        <button id="declineCookie">Decline</button>
    </div>
</div>
      <br>
      <div class="footer-links">
        
  <center>    <p class="mt-3">&copy; 2024 Madridejos Community College. All Rights Reserved.</p></center> 
    </div>
  </footer>

  <!-- jQuery, Bootstrap JS and Other Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/owl.carousel.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.carousel').carousel();
    });
    
// Check if the user has already accepted or declined cookies
if (!document.cookie.split(';').some((item) => item.trim().startsWith('cookie_consent='))) {
    // Display the consent banner
    document.getElementById("cookieConsent").style.display = "block";
}

// When the user accepts the cookies
document.getElementById("acceptCookie").addEventListener('click', function() {
    // Set a cookie to remember the user's consent
    document.cookie = "cookie_consent=true; max-age=" + 60*60*24*365 + "; path=/"; // Cookie expires in 1 year
    document.getElementById("cookieConsent").style.display = "none"; // Hide the banner
});

// When the user declines the cookies
document.getElementById("declineCookie").addEventListener('click', function() {
    // Simply hide the banner without setting the cookie
    document.getElementById("cookieConsent").style.display = "none"; // Hide the banner
});

  </script>
</body>

</html>
