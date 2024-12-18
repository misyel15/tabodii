<?php 
include 'db_connect.php'; 
session_start(); // Start the session

include 'includes/header.php';
// Check if the user is logged in and has a dept_id
if (!isset($_SESSION['username']) || !isset($_SESSION['dept_id'])) {
    header("Location: login"); // Redirect to login page if not logged in
    exit();
}

// Define arrays to hold the subject count per semester
$subjects_per_semester = [
    '1st Year - 1st Semester' => 0,
    '1st Year - 2nd Semester' => 0,
    '2nd Year - 1st Semester' => 0,
    '2nd Year - 2nd Semester' => 0,
    '3rd Year - 1st Semester' => 0,
    '3rd Year - 2nd Semester' => 0,
    '3rd Year - Summer' => 0,
    '4th Year - 1st Semester' => 0,
    '4th Year - 2nd Semester' => 0
];

// Get the department ID from session
$dept_id = $_SESSION['dept_id'];
// Query the database to count subjects per semester
$sql = "SELECT year, semester, COUNT(*) as subject_count FROM subjects WHERE dept_id = ? GROUP BY year, semester";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$query = $stmt->get_result();

while ($row = $query->fetch_assoc()) {
    $year = $row['year']; // Use the correct column name
    $semester = $row['semester'];
    
    // Map the result to the correct semester
    $key = "{$year} Year - {$semester} Semester";
    if (isset($subjects_per_semester[$key])) {
        $subjects_per_semester[$key] = $row['subject_count'];
    }
}

// Convert the PHP array to JSON format for use in JavaScript
$subjects_data = json_encode(array_values($subjects_per_semester));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/themes/default/jquery.mobile-1.4.5.min.css">
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: lightgray;
        }
        .main-container {
            background-color: white;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 98%;
            margin: 0 auto;
        }
        .card1 {
            background: #b1b0b0;
            color: #000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .card2 {
            background: #99cc99;
            color: #000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .card3 {
            background: #009DD1;
            color: #000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .card4 {
            background: #FF6384;
            color: #000;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .card-body {
            text-align: center;
        }
        .icon i {
            font-size: 3rem;
        }
        .chart-container {
            width: 100%;
            height: 0%;
            margin: 0 auto;
        }
        @media (max-width: 1200px) {
            .main-container {
                padding: 1rem;
            }
        }
        @media (max-width: 992px) {
            .card {
                margin-bottom: 0.5rem;
            }
        }
        @media (max-width: 768px) {
            .col-lg-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .container-fluid {
                padding: 0;
            }
            .card-body {
                padding: 1rem;
            }
        }
        @media (max-width: 576px) {
            .icon i {
                font-size: 2rem;
            }
            .card-body h3 {
                font-size: 1.5rem;
            }
        }

        /* CSS for smaller pie chart */
        .small-chart-container {
            width: 80%;
            height: 0%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container main-container" style="margin-top:100px;">
        <h3 class="my-4"> <p>Welcome, <?php echo $_SESSION['name']; ?>!</p></h3>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">
                    <div class="card1" style="box-shadow: 0 0 5px black;">
                        <div class="card-body">
                            <div class="icon" style="text-align:right;">
                                <i class="fa fa-4x fa-school text-white" aria-hidden="true"></i>
                            </div>
                            <?php
                                $sql = "SELECT * FROM roomlist WHERE dept_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $dept_id); // Bind the dept_id parameter
                                $stmt->execute();
                                $query = $stmt->get_result();
                                $num_rooms = $query->num_rows; // Number of rooms
                                echo "<h1>".$num_rooms."</h1>";
                            ?> 
                            <p>Number of Rooms</p>                
                            <hr>
                            <a class="medium text-white stretched-link" href="room">View Details</a>
                        </div>
                    </div>              
                </div>
                <div class="col-lg-3">
                    <div class="card2" style="box-shadow: 0 0 5px black;">
                        <div class="card-body">
                            <div class="icon" style="text-align:right;">
                                <i class="fa fa-4x fa-user-tie text-white" aria-hidden="true"></i>
                            </div>
                            <?php
                                $sql = "SELECT * FROM faculty WHERE dept_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $dept_id);
                                $stmt->execute();
                                $query = $stmt->get_result();
                                $num_instructors = $query->num_rows; // Number of instructors
                                echo "<h1>".$num_instructors."</h1>";
                            ?>
                            <p>Number of Instructors</p>  
                            <hr>
                            <a class="medium text-white  stretched-link" href="faculty">View Details</a>
                        </div>
                    </div>              
                </div>
                <div class="col-lg-3">
                    <div class="card3" style="box-shadow: 0 0 5px black;">
                        <div class="card-body">
                            <div class="icon" style="text-align:right;">
                                <i class="fa fa-4x fa-book-open text-white" aria-hidden="true"></i>
                            </div>
                            <?php
                                $sql = "SELECT * FROM subjects WHERE dept_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $dept_id);
                                $stmt->execute();
                                $query = $stmt->get_result();
                                $num_subjects = $query->num_rows; // Number of subjects
                                echo "<h1>".$num_subjects."</h1>";
                            ?>
                            <p>Number of Subjects</p>  
                            <hr>
                            <a class="medium text-white  stretched-link" href="subjects">View Details</a>
                        </div>
                    </div>              
                </div>
                <div class="col-lg-3">
                    <div class="card4" style="box-shadow: 0 0 5px black;">
                        <div class="card-body">
                            <div class="icon" style="text-align:right;">
                                <i class="fa fa-4x fa-graduation-cap text-white" aria-hidden="true"></i>
                            </div>
                            <?php
                                $sql = "SELECT * FROM courses WHERE dept_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $dept_id);
                                $stmt->execute();
                                $query = $stmt->get_result();
                                $num_courses = $query->num_rows; // Number of courses
                                echo "<h1>".$num_courses."</h1>";
                            ?>
                            <p>Number of Courses</p>  
                            <hr>
                            <a class="medium text-white  stretched-link" href="courses">View Details</a>
                        </div>
                    </div>              
                </div>
            </div>

            <!-- Bar Chart and Pie Chart Row -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card chart-container" >
                        <div class="card-header">
                            <h3>Number of Subjects Per Semester</h3>
                        </div>
                       <div class="card-body">
                            <canvas id="subjectsBarChart"></canvas>
                        </div> 
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card small-chart-container " >
                        <div class="card-header">
                            <h3>Subjects Distribution</h3>
                        </div>
                         <div class="card-body">
                            <canvas id="subjectsPieChart" width="200" height="200"></canvas>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function(){
            var subjectsData = <?php echo $subjects_data; ?>; // Fetch dynamic data from PHP

            // Bar Chart
            var ctxBar = document.getElementById('subjectsBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: [
                        '1st Year - 1st Semester', '1st Year - 2nd Semester', 
                        '2nd Year - 1st Semester', '2nd Year - 2nd Semester', 
                        '3rd Year - 1st Semester', '3rd Year - 2nd Semester', 
                        '3rd Year - Summer', '4th Year - 1st Semester', 
                        '4th Year - 2nd Semester'
                    ],
                    datasets: [{
                        label: 'Number of Subjects',
                        data: subjectsData,
                        backgroundColor: 'skyblue',
                        borderColor: 'rgba(0, 0, 0, 0)',
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });

            // Pie Chart
            var ctxPie = document.getElementById('subjectsPieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: [
                        '1st Year - 1st Sem', '1st Year - 2nd Sem', 
                        '2nd Year - 1st Sem', '2nd Year - 2nd Sem', 
                        '3rd Year - 1st Sem', '3rd Year - 2nd Semester', 
                        '3rd Year - Sum', '4th Year - 1st Sem', 
                        '4th Year - 2nd Sem'
                    ],
                    datasets: [{
                        label: 'Subjects Distribution',
                        data: subjectsData,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
                            '#FF9F40', '#4CAF50', '#FF6F61', '#33C3F0'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
