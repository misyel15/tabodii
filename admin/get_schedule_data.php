<?php
// Database connection
require_once 'db_connect.php'; // Ensure you include your database connection here

// fetch_schedule_data.php
if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];
    $result = $conn->query("SELECT * FROM schedule WHERE id = '$schedule_id'");
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);  // Return data as JSON
    }
}

?>
