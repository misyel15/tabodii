<?php

include "db_connect.php";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['faculty'], $_POST['semester'], $_POST['course'], $_POST['subject'], $_POST['room_name'], $_POST['days'], $_POST['timeslot'])) {

        $id = $_POST['id'];
        $faculty = $_POST['faculty'];
        $semester = $_POST['semester'];
        $course = $_POST['course'];
        $subject = $_POST['subject'];
        $room_name = $_POST['room_name'];
        $days = $_POST['days'];
        $timeslot = $_POST['timeslot'];

        // Fetch the existing dept_id
        $stmt = $conn->prepare("SELECT dept_id FROM loading WHERE id = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($existing_dept_id);
        if (!$stmt->fetch()) {
            die("Error: No record found for ID $id");
        }
        $stmt->close();

        $dept_id = $existing_dept_id;

        // Prepare the UPDATE statement
        $stmt = $conn->prepare("UPDATE loading 
                                SET dept_id = ?, timeslot_id = NULL, timeslot = ?, rooms = NULL, room_name = ?, faculty = ?, 
                                    course = ?, subjects = ?, days = ?, sub_description = NULL, total_units = NULL, lec_units = NULL, 
                                    lab_units = NULL, hours = NULL, coursedesc = NULL, timeslot_sid = NULL, semester = ? 
                                WHERE id = ?");
        if (!$stmt) {
            die("Error preparing update statement: " . $conn->error);
        }

        // Bind parameters for the prepared statement
        // Updated to match the number of variables passed
        $stmt->bind_param("ssssssssssi", 
            $dept_id, $timeslot, $room_name, $faculty, $course, $subject, $days, $semester, $id);

        if ($stmt->execute()) {
            echo "Schedule entry updated successfully!";
            header("Location: roomassigntry");
            exit;
        } else {
            die("Update failed: " . $stmt->error);
        }
    } else {
        die("Error: Missing required fields.");
    }
} else {
    die("Invalid request method.");
}
?>
