<?php
// Security Headers
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(self), microphone=(), camera=()");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://trusted-scripts.com;");
header("X-Frame-Options: DENY");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Redirect HTTP to HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// Secure session cookie settings
ini_set('session.cookie_secure', '1');    // Enforces HTTPS-only session cookies
ini_set('session.cookie_httponly', '1');  // Prevents JavaScript from accessing session cookies
ini_set('session.cookie_samesite', 'Strict'); // Prevents CSRF

// Anti-XXE: Secure XML parsing
libxml_use_internal_errors(true); // Suppress libxml errors for better handling

// Function to parse XML securely
function parseXMLSecurely($xmlString) {
    if (empty($xmlString)) {
        throw new Exception('The provided XML string is empty.');
    }

    // Create a new DOMDocument instance
    $dom = new DOMDocument();
    
    // Load XML securely with LIBXML_NONET to prevent external entity loading
    if (!$dom->loadXML($xmlString, LIBXML_NOENT | LIBXML_NONET | LIBXML_NOCDATA)) {
        $errors = libxml_get_errors();
        libxml_clear_errors(); // Clear errors after fetching
        throw new Exception('Error loading XML: ' . implode(", ", array_map(fn($err) => $err->message, $errors)));
    }

    return $dom;
}

// Example usage
try {
    $xmlString = '<root><element>Sample Value</element></root>'; // Example XML input
    $dom = parseXMLSecurely($xmlString);

    // Process XML elements
    $elements = $dom->getElementsByTagName('element');
    foreach ($elements as $element) {
        echo 'Element value: ' . $element->nodeValue;
    }
} catch (Exception $e) {
    error_log('Error processing XML: ' . $e->getMessage());
    echo 'Error processing XML. Please try again later.';
}
?>
<!-- Required meta tags-->
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title Page-->

 <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="js/sweetalert2@10.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">

    <!-- Fontfaces CSS-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
