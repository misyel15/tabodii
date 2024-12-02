<?php

include "db_connect.php";

// Ensure that the form is being submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the necessary fields are set
    if (isset($_POST['id']) && isset($_POST['faculty']) && isset($_POST['semester']) && isset($_POST['course']) &&
        isset($_POST['subject']) && isset($_POST['room_name']) && isset($_POST['days']) && 
        isset($_POST['timeslot'])) {  // Updated: timeslot instead of timeslot_sid

        // Get the form values
        $id = $_POST['id']; // The unique ID of the schedule entry being edited
        $faculty = $_POST['faculty'];
        $semester = $_POST['semester'];
        $course = $_POST['course'];
        $subject = $_POST['subject'];
        $room_name = $_POST['room_name'];  // Room name
        $days = $_POST['days'];
        $timeslot = $_POST['timeslot']; // Updated: timeslot instead of timeslot_sid

        // Fetch the existing dept_id if not provided in the form
        $stmt = $conn->prepare("SELECT dept_id FROM loading WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($existing_dept_id);
        $stmt->fetch();
        $stmt->close();

        // Retain the existing dept_id
        $dept_id = $existing_dept_id;

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE loading 
                                SET dept_id = ?, timeslot_id = NULL, timeslot = ?, rooms = NULL, room_name = ?, faculty = ?, 
                                    course = ?, subjects = ?, days = ?, sub_description = NULL, total_units = NULL, lec_units = NULL, 
                                    lab_units = NULL, hours = NULL, coursedesc = NULL, timeslot_sid = NULL, semester = ? 
                                WHERE id = ?");
        
        // Bind parameters for the prepared statement
        $stmt->bind_param("sssssssssi", 
            $dept_id, $timeslot, $room_name, $faculty, $course, $subject, $days, $semester, $id);

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
