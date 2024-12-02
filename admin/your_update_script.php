<?php
include "db_connect.php";

// Ensure that the form is submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if all required fields are set
    $requiredFields = ['id', 'faculty', 'semester', 'course', 'subject', 'room_name', 'days', 'timeslot'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo "Error: Missing required fields.";
            exit;
        }
    }

    // Get the form values
    $id = $_POST['id'];
    $faculty = $_POST['faculty'];
    $semester = $_POST['semester'];
    $course = $_POST['course'];
    $subject = $_POST['subject'];
    $room_name = $_POST['room_name'];
    $days = $_POST['days'];
    $timeslot = $_POST['timeslot'];

    // Fetch `dept_id` automatically based on `faculty`
    $stmt = $conn->prepare("SELECT dept_id FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $faculty);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $dept_id = $row['dept_id'];
    } else {
        echo "Error: Faculty not found.";
        exit;
    }

    $stmt->close();

    // Update the schedule
    $stmt = $conn->prepare("UPDATE loading 
        SET dept_id = ?, faculty = ?, semester = ?, course = ?, subjects = ?, room_name = ?, days = ?, timeslot = ? 
        WHERE id = ?");
    $stmt->bind_param("ssssssssi", $dept_id, $faculty, $semester, $course, $subject, $room_name, $days, $timeslot, $id);

    if ($stmt->execute()) {
        echo "Schedule entry updated successfully!";
        header("Location: roomassigntry"); // Adjust this URL as needed
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}
?>
