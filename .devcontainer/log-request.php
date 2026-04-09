<?php

// Define log file path
$logFile = __DIR__ . '/request_log.txt';

// --- 1. Sanitize and Validate Input ---
// Safely extract and sanitize query parameters
$input = [];
foreach ($_GET as $key => $value) {
    // Strip tags to remove HTML/JS injection
    // Trim whitespace
    $cleanKey = trim(strip_tags($key));
    $cleanValue = trim(strip_tags($value));
    
    // Basic validation
    if (!empty($cleanKey)) {
        $input[$cleanKey] = $cleanValue;
    }
}

// --- 2. Safely Read Headers and Cookies ---
$headers = getallheaders();

// Read Cookies directly (Do not hash)
$cookies = $_COOKIE;

// --- 3. Log Metadata ---
$logEntry = "--------------------------------------------------\n";
$logEntry .= "TIMESTAMP: " . date('Y-m-d H:i:s') . "\n";
$logEntry .= "IP ADDRESS: " . $_SERVER['REMOTE_ADDR'] . "\n";
$logEntry .= "USER AGENT: " . (isset($headers['User-Agent']) ? $headers['User-Agent'] : 'N/A') . "\n";

// Log Headers
$logEntry .= "HEADERS: \n";
foreach ($headers as $name => $value) {
    $logEntry .= "  $name: $value\n";
}

// Log Cookies (Unhashed)
$logEntry .= "COOKIES: \n";
foreach ($cookies as $name => $value) {
    $logEntry .= "  $name: $value\n";
}

// Log Input
$logEntry .= "INPUT (Query Params): \n";
foreach ($input as $key => $value) {
    $logEntry .= "  $key: $value\n";
}

$logEntry .= "--------------------------------------------------\n\n";

// Write to file (append mode)
file_put_contents($logFile, $logEntry, FILE_APPEND);

// --- 4. Return Response ---
header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "message" => "Request logged",
    "logged_at" => date('Y-m-d H:i:s')
]);
?>
