<?php
include "db_connect.php";

// Ensure that the form is being submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the necessary fields are set
    $required_fields = ['id', 'faculty', 'semester', 'course', 'subject', 'room_name', 'days', 'timeslot'];
    foreach ($required_fields as $field) {
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

    // Automatically fetch dept_id based on faculty
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

    // Optional fields
    $timeslot_id = null;
    $rooms = null;
    $sub_description = null;
    $total_units = null;
    $lec_units = null;
    $lab_units = null;
    $hours = null;
    $coursedesc = null;

    // Use prepared statements to update the database
    $stmt = $conn->prepare("UPDATE loading 
                            SET dept_id = ?, timeslot_id = ?, timeslot = ?, rooms = ?, room_name = ?, faculty = ?, 
                                course = ?, subjects = ?, days = ?, sub_description = ?, total_units = ?, lec_units = ?, 
                                lab_units = ?, hours = ?, coursedesc = ?, semester = ? 
                            WHERE id = ?");
    $stmt->bind_param(
        "ssssssssssssssssi",
        $dept_id,
        $timeslot_id,
        $timeslot,
        $rooms,
        $room_name,
        $faculty,
        $course,
        $subject,
        $days,
        $sub_description,
        $total_units,
        $lec_units,
        $lab_units,
        $hours,
        $coursedesc,
        $semester,
        $id
    );

    if ($stmt->execute()) {
        echo "Schedule entry updated successfully!";
        header("Location: roomassigntry"); // Adjust the URL as needed
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request method.";
}
?>
