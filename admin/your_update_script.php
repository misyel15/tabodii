<?php
// Include your database connection file
require_once 'db_connection.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate if required fields are present
    if (empty($_POST['edit_id']) || empty($_POST['faculty']) || empty($_POST['semester']) || 
        empty($_POST['course']) || empty($_POST['subject']) || empty($_POST['room_name']) || 
        empty($_POST['days']) || empty($_POST['timeslot'])) {
        die('Error: Missing required fields.');
    }

    // Retrieve form data and sanitize it
    $edit_id = intval($_POST['edit_id']);
    $dept_id = intval($_POST['dept_id']);
    $faculty_id = intval($_POST['faculty']);
    $semester = htmlspecialchars($_POST['semester']);
    $course = htmlspecialchars($_POST['course']);
    $subject = htmlspecialchars($_POST['subject']);
    $room_id = intval($_POST['room_name']);
    $days = htmlspecialchars($_POST['days']);
    $timeslot_id = intval($_POST['timeslot']);

    try {
        // Prepare the SQL query for updating the schedule entry
        $stmt = $conn->prepare("
            UPDATE schedules 
            SET faculty_id = ?, semester = ?, course = ?, subject = ?, room_id = ?, days = ?, timeslot_id = ?
            WHERE id = ? AND dept_id = ?
        ");
        $stmt->bind_param("isssisiis", $faculty_id, $semester, $course, $subject, $room_id, $days, $timeslot_id, $edit_id, $dept_id);

        // Execute the query
        if ($stmt->execute()) {
            echo 'Schedule updated successfully.';
        } else {
            throw new Exception('Failed to update schedule. ' . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        // Handle errors
        echo 'Error: ' . $e->getMessage();
    }
}

// Close the database connection
$conn->close();
?>
