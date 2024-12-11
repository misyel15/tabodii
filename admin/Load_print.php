<?php
// Display errors (for debugging only — remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection file
include('db_connect.php');

// Get the department ID from the session, ensuring it's an integer
$dept_id = isset($_SESSION['dept_id']) ? intval($_SESSION['dept_id']) : null;

// Get the faculty ID from the URL parameter and ensure it's an integer
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Check for valid faculty ID and department ID
if (!$id) die("Invalid faculty ID.");
if (!$dept_id) die("Department ID is not set.");

// Function to generate table rows based on the faculty ID
function generateTableContent($conn, $id) {
    $content = '';
    $sumtu = 0;
    $sumh = 0;

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM loading WHERE faculty = ? ORDER BY timeslot_sid ASC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($lrow = $result->fetch_assoc()) {
        $days = htmlspecialchars($lrow['days']);
        $timeslot = htmlspecialchars($lrow['timeslot']);
        $course = htmlspecialchars($lrow['course']);
        $subject_code = htmlspecialchars($lrow['subjects']);
        $room_id = intval($lrow['rooms']);
        $fid = intval($lrow['faculty']);

        // Faculty details
        $faculty = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM faculty WHERE id = $fid");
        $frow = $faculty->fetch_assoc();
        $instname = $frow ? htmlspecialchars($frow['name']) : "Unknown Instructor";

        // Subject details
        $subjects = $conn->query("SELECT * FROM subjects WHERE subject = '" . $conn->real_escape_string($subject_code) . "'");
        $srow = $subjects->fetch_assoc();
        $description = $srow ? htmlspecialchars($srow['description']) : "Unknown Subject";
        $units = $srow ? intval($srow['total_units']) : 0;
        $lec_units = $srow ? intval($srow['Lec_Units']) : 0;
        $lab_units = $srow ? intval($srow['Lab_Units']) : 0;
        $hours = $srow ? intval($srow['hours']) : 0;

        $sumh += $hours;
        $sumtu += $units;

        // Room details
        $rooms = $conn->query("SELECT * FROM roomlist WHERE id = $room_id");
        $roomrow = $rooms->fetch_assoc();
        $room_name = $roomrow ? htmlspecialchars($roomrow['room_name']) : "Unknown Room";

        // Generate the row content
        $content .= '<tr>
            <td align="center">' . $subject_code . '</td>
            <td align="center">' . $description . '</td>
            <td align="center">' . $days . '</td>
            <td align="center">' . $timeslot . '</td>
            <td align="center">' . $course . '</td>
            <td align="center">' . $lec_units . '</td>
            <td align="center">' . $lab_units . '</td>
            <td align="center">' . $units . '</td>
            <td align="center">' . $hours . '</td>
        </tr>';
    }

    // Add total units and hours row
    $content .= '<tr>
        <td colspan="7" style="text-align:right;"><strong>Total Number of Units/Hours (Basic)</strong></td>
        <td align="center">' . $sumtu . '</td>
        <td align="center">' . $sumh . '</td>
    </tr>';

    return $content;
}

// Function to determine the header image based on department ID
function getHeaderImage($dept_id) {
    $headerMap = [
        4444 => "assets/uploads/end.png",
        5555 => "assets/uploads/EDU.png",
        3333 => "assets/uploads/HM.png",
        12345 => "assets/uploads/BA.png"
    ];
    return isset($headerMap[$dept_id]) ? $headerMap[$dept_id] : 'assets/uploads/default_header.png';
}

// Function to print the page content
function printPage($conn, $id, $dept_id) {
    // Fetch the instructor's name
    $faculty = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM faculty WHERE id = $id");
    if (!$faculty) die("Query failed: " . $conn->error);
    $frow = $faculty->fetch_assoc();
    $instname = $frow ? htmlspecialchars($frow['name']) : "Unknown Instructor";

    // Get the header image
    $headerImage = getHeaderImage($dept_id);

    // Generate the table content
    $content = generateTableContent($conn, $id);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instructor's Load</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
            .header { display: flex; justify-content: center; align-items: center; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin: 0 auto; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
            th { background-color: #f2f2f2; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            tr:hover { background-color: #e2e2e2; }
            .header img { width: 100%; height: auto; }
            .instname { font-weight: bold; font-size: 18px; margin-bottom: 10px; }
        </style>
    </head>
    <body onload="window.print()">
        <div class="header">
            <img src="<?php echo htmlspecialchars($headerImage); ?>" alt="Department Logo">
        </div>

        <div class="instname">
            Instructor's Load: <?php echo htmlspecialchars($instname); ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Description</th>
                    <th>Days</th>
                    <th>Timeslot</th>
                    <th>Course</th>
                    <th>Lec Units</th>
                    <th>Lab Units</th>
                    <th>Total Units</th>
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $content; ?>
            </tbody>
        </table>

        <script>
            // Detect when the print dialog is closed
            window.onafterprint = function() {
                window.history.back();
            };
        </script>
    </body>
    </html>
    <?php
}

// Call the function to display the print page
if ($id) {
    printPage($conn, $id, $dept_id);
} else {
    echo "No faculty ID provided.";
}
?>
