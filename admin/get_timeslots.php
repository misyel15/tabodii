<?php
// Assuming you are using a database connection
include 'db_connection.php';

if (isset($_POST['day']) && isset($_POST['dept_id'])) {
    $day = $_POST['day'];
    $deptId = $_POST['dept_id'];

    // Query the database to fetch timeslots for the selected day and department
    $query = "SELECT * FROM timeslot WHERE days = '$day' AND dept_id = '$deptId'"; 
    $result = $conn->query($query);

    // Generate HTML options for the timeslot dropdown
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['timeslot'] . ' ' . $row['schedule'] . '</option>';
        }
    } else {
        echo '<option value="">No timeslots available</option>';
    }
}
?>
