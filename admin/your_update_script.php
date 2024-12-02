<?php

include "db_connect.php";
// Ensure that the form is being submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the 'id' (which uniquely identifies the schedule entry) and other necessary fields are set
    if (isset($_POST['id']) && isset($_POST['faculty']) && isset($_POST['semester']) && isset($_POST['course']) &&
        isset($_POST['yrsection']) && isset($_POST['subject']) && isset($_POST['room']) && isset($_POST['days']) && 
        isset($_POST['timeslot']) && isset($_POST['timeslot_sid'])) {

        // Get the form values
        $id = $_POST['id']; // The unique ID of the schedule entry being edited
        $dept_id = $_POST['dept_id']; // Department ID
        $faculty = $_POST['faculty'];
        $semester = $_POST['semester'];
        $course = $_POST['course'];
        $yrsection = $_POST['yrsection'];
        $subject = $_POST['subject'];
        $room = $_POST['room'];
        $room_name = $_POST['room_name'];  // Room name (ensure this exists in the form)
        $days = $_POST['days'];
        $timeslot = $_POST['timeslot'];
        $sub_description = isset($_POST['description']) ? $_POST['description'] : '';
        $total_units = isset($_POST['total_units']) ? $_POST['total_units'] : '';
        $lec_units = isset($_POST['lec_units']) ? $_POST['lec_units'] : '';
        $lab_units = isset($_POST['lab_units']) ? $_POST['lab_units'] : '';
        $hours = isset($_POST['hours']) ? $_POST['hours'] : '';
        $coursedesc = isset($_POST['coursedesc']) ? $_POST['coursedesc'] : '';
        $timeslot_sid = $_POST['timeslot_sid'];

        // Assuming you already have a database connection in $conn
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE loading 
                                SET dept_id = ?, timeslot_id = ?, timeslot = ?, rooms = ?, room_name = ?, faculty = ?, course = ?, 
                                    subjects = ?, days = ?, sub_description = ?, total_units = ?, lec_units = ?, lab_units = ?, 
                                    hours = ?, coursedesc = ?, timeslot_sid = ?, semester = ? 
                                WHERE id = ?");
        
        // Bind parameters for the prepared statement
        $stmt->bind_param("iissssssssssssssi", 
            $dept_id, $timeslot, $timeslot_id, $room, $room_name, $faculty, $course, 
            $subject, $days, $sub_description, $total_units, $lec_units, $lab_units, 
            $hours, $coursedesc, $timeslot_sid, $semester, $id); // Use $id for the WHERE clause

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
