<?php
session_start();
//include "database/scheduling_db.sql"; // Ensure this file contains the database connection
include "db_connect.php";

// Assuming you store the department ID in the session during login
$dept_id = $_SESSION['dept_id'] ?? null; // Get the department ID from the session

// Check if dept_id is set
if (!$dept_id) {
    echo "<script>alert('Department ID is not set. Please log in again.');</script>";
}
// Ensure that the form is being submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the 'edit_id' and other necessary fields are set
    if (isset($_POST['edit_id']) && isset($_POST['faculty']) && isset($_POST['semester']) && isset($_POST['course']) &&
        isset($_POST['yrsection']) && isset($_POST['subject']) && isset($_POST['room']) && isset($_POST['days']) && 
        isset($_POST['timeslot'])) {

        // Get the form values
        $edit_id = $_POST['edit_id']; // The ID of the schedule entry being edited
        $faculty = $_POST['faculty'];
        $semester = $_POST['semester'];
        $course = $_POST['course'];
        $yrsection = $_POST['yrsection'];
        $subject = $_POST['subject'];
        $room = $_POST['room'];
        $days = $_POST['days'];
        $timeslot = $_POST['timeslot'];

        // Optional: Get other fields
        $sub_description = isset($_POST['description']) ? $_POST['description'] : '';
        $total_units = isset($_POST['total_units']) ? $_POST['total_units'] : '';
        $lec_units = isset($_POST['lec_units']) ? $_POST['lec_units'] : '';
        $lab_units = isset($_POST['lab_units']) ? $_POST['lab_units'] : '';
        $hours = isset($_POST['hours']) ? $_POST['hours'] : '';
        $coursedesc = isset($_POST['coursedesc']) ? $_POST['coursedesc'] : '';
        $timeslot_sid = isset($_POST['timeslot_sid']) ? $_POST['timeslot_sid'] : '';

        // Assuming you already have a database connection in $conn
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE loading 
                                SET faculty = ?, semester = ?, course = ?, yrsection = ?, subjects = ?, rooms = ?, 
                                    room_name = ?, days = ?, sub_description = ?, total_units = ?, lec_units = ?, lab_units = ?, 
                                    hours = ?, coursedesc = ?, timeslot_id = ?, timeslot_sid = ? 
                                WHERE id = ?");
        
        // Bind parameters for the prepared statement
        $stmt->bind_param("ssssssssssssssssi", 
            $faculty, $semester, $course, $yrsection, $subject, $room, 
            $room, $days, $sub_description, $total_units, $lec_units, $lab_units, 
            $hours, $coursedesc, $timeslot, $timeslot_sid, $edit_id);

        // Execute the update query
        if ($stmt->execute()) {
            // If the update is successful, redirect back or show a success message
            echo "Schedule entry updated successfully!";
            // Optionally redirect to another page
            header("Location: roomassigntry"); // Redirect to another page (adjust URL as needed)
            exit;
        } else {
            // If there was an error with the update, display an error message
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();

    } else {
        // Handle the case where required fields are missing
        echo "Error: Missing required fields.";
    }
} else {
    // If the request method is not POST, show an error
    echo "Invalid request method.";
}
?>
