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

        // Automatically fetch dept_id based on faculty
        // Assuming you have a 'faculty' table with a 'dept_id' column
        $stmt = $conn->prepare("SELECT dept_id FROM faculty WHERE id = ?");
        $stmt->bind_param("i", $faculty);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $dept_id = $row['dept_id'];  // Automatically get the department ID based on faculty
        } else {
            echo "Error: Faculty not found.";
            exit;
        }

        // Set other columns to NULL if not included in the form
        $timeslot_id = NULL; // Not included in the form
        $rooms = NULL; // Not included in the form
        $sub_description = NULL; // Not included in the form
        $total_units = NULL; // Not included in the form
        $lec_units = NULL; // Not included in the form
        $lab_units = NULL; // Not included in the form
        $hours = NULL; // Not included in the form
        $coursedesc = NULL; // Not included in the form

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE loading 
                                SET dept_id = ?, timeslot_id = ?, timeslot = ?, rooms = ?, room_name = ?, faculty = ?, 
                                    course = ?, subjects = ?, days = ?, sub_description = ?, total_units = ?, lec_units = ?, 
                                    lab_units = ?, hours = ?, coursedesc = ?, timeslot_sid = ?, semester = ? 
                                WHERE id = ?");
        
        // Bind parameters for the prepared statement
        $stmt->bind_param("sssssssssssssssssi", 
            $dept_id, $timeslot_id, $timeslot, $rooms, $room_name, $faculty, $course, $subject, $days, 
            $sub_description, $total_units, $lec_units, $lab_units, $hours, $coursedesc, $timeslot, $semester, $id);

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
