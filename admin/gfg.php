<?php
include('db_connect.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Read POST data
$postData = json_decode(file_get_contents("php://input"));
$request = isset($postData->request) ? $postData->request : "";

// Initialize result arrays
$data = array();

try {
    // Handle requests
    switch ($request) {
        case 'getYear':
            if (!empty($postData->course)) {
                $course = $postData->course;

                $sql = "SELECT * FROM section WHERE course=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $course);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        "id" => $row['year'] . $row['section'],
                        "name" => $row['year'] . $row['section'],
                        "course" => $row['course'],
                        "year" => $row['year']
                    );
                }
            }
            break;

        case 'getSubjects':
            if (!empty($postData->course) && !empty($postData->year) && !empty($postData->semester)) {
                $course = $postData->course;
                $year = $postData->year;
                $semester = $postData->semester;

                $sql = "SELECT * FROM subjects WHERE course=? AND year=? AND semester=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $course, $year, $semester);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        "id" => $row['subject'],
                        "name" => $row['subject'],
                        "specialization" => $row['specialization']
                    );
                }
            }
            break;

        // Additional cases follow the same pattern...

        default:
            throw new Exception("Invalid request type.");
    }

    // Output JSON response
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

// Close database connection if set
if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();
die();
?>
