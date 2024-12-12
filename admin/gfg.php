<?php
include('db_connect.php');

// Get the department ID from the session
$dept_id = $_SESSION['dept_id']; // Assuming dept_id is set during login

// Read POST data
$postData = json_decode(file_get_contents("php://input"));
$request = isset($postData->request) ? $postData->request : "";

// Initialize result arrays
$result = array();
$data = array();

// Handle requests
switch ($request) {
   case 'getYear':
      if (isset($postData->course)) {
         $course = $postData->course;

         $sql = "SELECT * FROM section WHERE course=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("si", $course, $dept_id);
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
      if (isset($postData->course, $postData->year, $postData->semester)) {
         $course = $postData->course;
         $year = $postData->year;
         $semester = $postData->semester;

         $sql = "SELECT * FROM subjects WHERE course=? AND year=? AND semester=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("sssi", $course, $year, $semester, $dept_id);
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

   case 'getTimeslot':
      if (isset($postData->days, $postData->specialization)) {
         $days = $postData->days;
         $specialization = $postData->specialization;

         $sql = "SELECT * FROM timeslot WHERE schedule=? AND specialization=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("ssi", $days, $specialization, $dept_id);
         $stmt->execute();
         $result = $stmt->get_result();

         while ($row = $result->fetch_assoc()) {
            $data[] = array(
               "id" => $row['id'],
               "name" => $row['timeslot'] . " " . $row['schedule']
            );
         }
      }
      break;

   case 'getSection':
      if (isset($postData->section_id)) {
         $section_id = $postData->section_id;

         $sql = "SELECT * FROM section WHERE year=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("si", $section_id, $dept_id);
         $stmt->execute();
         $result = $stmt->get_result();

         while ($row = $result->fetch_assoc()) {
            $data[] = array(
               "id" => $row['section'],
               "name" => $row['section']
            );
         }
      }
      break;

   case 'getDesc':
      if (isset($postData->id)) {
         $id = $postData->id;

         $sql = "SELECT * FROM subjects WHERE subject=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("si", $id, $dept_id);
         $stmt->execute();
         $result = $stmt->get_result();

         while ($row = $result->fetch_assoc()) {
            $data[] = array(
               "description" => $row['description'],
               "total_units" => $row['total_units'],
               "lec_units" => $row['Lec_Units'],
               "lab_units" => $row['Lab_Units']
            );
         }
      }
      break;

   case 'getRoomName':
      if (isset($postData->id)) {
         $id = $postData->id;

         $sql = "SELECT * FROM roomlist WHERE room_id=? AND dept_id=?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("ii", $id, $dept_id);
         $stmt->execute();
         $result = $stmt->get_result();

         while ($row = $result->fetch_assoc()) {
            $data[] = array(
               "id" => $row['id'],
               "room_name" => $row['room_name']
            );
         }
      }
      break;

   default:
      // Handle unrecognized requests
      break;
}

// Output JSON response
echo json_encode($data);

// Close database connection
$stmt->close();
$conn->close();
?>
