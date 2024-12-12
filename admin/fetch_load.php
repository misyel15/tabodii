<?php
include 'db_connect.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Join the `loading` and `faculty` tables to get the desired information
    $stmt = $conn->prepare("
        SELECT 
            l.*, 
            CONCAT(f.lastname, ', ', f.firstname, ' ', f.middlename) AS full_name
        FROM 
            loading l
        JOIN 
            faculty f ON l.faculty = f.id
        WHERE 
            l.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Extract year and section from the course code (assuming a consistent pattern)
        $course_code = $row['course'];
        $year = substr($course_code, 0, 3);
        $section = substr($course_code, 3);

        // Add the extracted year and section to the response data
        $row['year'] = $year;
        $row['section'] = $section;

        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No data found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
