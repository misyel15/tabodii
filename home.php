<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Mcc Faculty Scheduling</title>
 
  <!-- Include Bootstrap for styling -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

  <!-- Include SweetAlert -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

  <?php include('./header.php'); ?>

  <?php 
  if(isset($_SESSION['login_id']))
    header("location:index.php");
  ?>
<style>
  body {
    width: 100%;
    height: calc(100%);
    position: fixed;
    margin: 0; /* Remove default body margin */
  }

  #main {
    width: calc(100%);
    height: calc(100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative; /* Required for positioning the overlay */
    overflow: hidden; /* Prevent overflow */
  }

  #main::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #eae6f5;
    background-size: cover; /* Cover the entire section */
    background-position: center; /* Center the image */
    opacity: 0.5; /* Adjust transparency (0.0 to 1.0) */
    z-index: 1; /* Place it behind the content */
  }

  #login {
    position: relative; /* Required to place it above the background */
    z-index: 2; /* Place it above the overlay */
  }

  /* Custom card styles */
  .card {
    border: none; /* Remove border */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Custom shadow */
  }

  .card-body {
    padding: 2rem; /* Add padding inside the card */
  }

  .btn-primary {
    background-color: #007bff; /* Custom button color */
    border: none; /* Remove button border */
    transition: background-color 0.3s; /* Smooth transition for hover effect */
  }

  .btn-primary:hover {
    background-color: #0056b3; /* Darker color on hover */
  }
</style>

</head>

<body>
<main id="main" class="bg-light">
  <div id="login" class="col-md-4">
    <div class="card position-relative"> <!-- Added position-relative -->
   
     
      <div class="card-body">
        <form id="login-form">
          <div class="login-logo text-center">
            <a>
              <img src="mcclogo.jpg" style="width:150px; height:90px;" alt="CoolAdmin">
            </a>
            <h3> Welcome To Mcc Faculty Scheduling</h3>
          </div>
          <div class="form-group">
            <label for="id_no" class="control-label">Please enter your Faculty ID No.</label>
            <input type="number" id="id_no" name="id_no" class="form-control">
          </div>
          <center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary">Login</button></center>
        </form>
        <br>
      </div>
    </div>
  </div>
</main>




  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

  <!-- Include Bootstrap JS and jQuery for functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <!-- Include SweetAlert JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

  <script>
    $('#login-form').submit(function(e) {
      e.preventDefault();
      $('#login-form button[type="submit"]').attr('disabled', true).html('Logging in...');
      $.ajax({
        url: 'admin/ajax.php?action=login_faculty',
        method: 'POST',
        data: $(this).serialize(),
        error: err => {
          console.log(err);
          $('#login-form button[type="submit"]').removeAttr('disabled').html('Login');
        },
        success: function(resp) {
          if (resp == 1) {
            location.href = 'index.php';
          } else {
            // Use SweetAlert for the alert message
            swal("Error!", "ID Number is incorrect.", "error");
            $('#login-form button[type="submit"]').removeAttr('disabled').html('Login');
          }
        }
      });
    });
  </script>

</body>
</html>
