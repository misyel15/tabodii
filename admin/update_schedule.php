<?php
include 'db_connect.php'; // Ensure you have included your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve POST data
    $id = $_POST['load_id'];
    $dept_id = $_POST['faculty'];
    $timeslot_id = $_POST['timeslot_id'];
    $rooms = $_POST['room'];
    $course = $_POST['course'];
    $subject = $_POST['subject'];
    $total_units = $_POST['total_units']; // Ensure this is included in the form
    $lab_units = $_POST['lab_units']; // Ensure this is included in the form
    $hours = $_POST['hours']; // Ensure this is included in the form
    $semester = $_POST['semester'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE loading
        SET dept_id = ?, timeslot_id = ?, rooms = ?, course = ?, 
            subject = ?, total_units = ?, lab_units = ?, 
            hours = ?, semest = ?
        WHERE id = ?");

    // Bind parameters
    $stmt->bind_param("iisssssssi", $dept_id, $timeslot_id, $rooms, 
                      $course, $subject, $total_units, 
                      $lab_units, $hours, $semester, $id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}
?>
