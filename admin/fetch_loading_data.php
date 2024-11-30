<?php
include 'db_connect.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $loading_query = $conn->query("SELECT * FROM loading WHERE id = '$id'");
    
    if ($loading_query) {
        $loading_data = $loading_query->fetch_assoc();
        if ($loading_data) {
            echo json_encode($loading_data);
        } else {
            echo json_encode(['error' => 'No data found']);
        }
    } else {
        echo json_encode(['error' => 'Error executing query: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'ID not set']);
}
?>
