<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('db_connect.php');

$dept_id = isset($_SESSION['dept_id']) ? intval($_SESSION['dept_id']) : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) die("Invalid faculty ID.");
if (!$dept_id) die("Department ID not set.");

// Generate table content
function generateTableContent($conn, $id) {
    $content = '';
    $sumtu = 0;
    $sumh = 0;

    $loads = $conn->query("SELECT * FROM loading WHERE faculty='$id' ORDER BY timeslot_sid ASC");
    if (!$loads) die("Query failed: " . $conn->error);

    while ($lrow = $loads->fetch_assoc()) {
        // process rows (omitted for brevity)
    }

    return $content;
}

function getHeaderImage($dept_id) {
    $headerMap = [
        4444 => "assets/uploads/end.png",
        5555 => "assets/uploads/EDU.png",
        3333 => "assets/uploads/HM.png",
        12345 => "assets/uploads/BA.png"
    ];
    return file_exists($headerMap[$dept_id]) ? $headerMap[$dept_id] : 'assets/uploads/default_header.png';
}

function printPage($conn, $id, $dept_id) {
    $faculty = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM faculty WHERE id=$id");
    if (!$faculty) die("Query failed: " . $conn->error);

    $frow = $faculty->fetch_assoc();
    $instname = $frow ? $frow['name'] : "Unknown Instructor";
    $headerImage = getHeaderImage($dept_id);
    $content = generateTableContent($conn, $id);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Instructor's Load</title>
    </head>
    <body onload="window.print()">
        <div class="header"><img src="<?php echo htmlspecialchars($headerImage); ?>" alt="Header"></div>
        <div class="instname">Instructor's Load: <?php echo htmlspecialchars($instname); ?></div>
        <table><?php echo $content; ?></table>
    </body>
    </html>
    <?php
}

if ($id) {
    printPage($conn, $id, $dept_id);
} else {
    echo "No faculty ID provided.";
}
?>
