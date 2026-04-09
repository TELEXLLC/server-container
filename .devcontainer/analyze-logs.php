<?php

$logFile = __DIR__ . '/request_log.txt';

if (!file_exists($logFile)) {
    die("No logs found. Make some requests first!\n");
}

// Parse log entries
$entries = [];
$currentEntry = [];
$inEntry = false;

$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    if (strpos($line, 'TIMESTAMP:') === 0) {
        if ($inEntry && !empty($currentEntry)) {
            $entries[] = $currentEntry;
        }
        $currentEntry = [
            'timestamp' => str_replace('TIMESTAMP: ', '', $line),
            'ip' => null,
            'user_agent' => null,
            'headers' => [],
            'cookies' => [],
            'input' => []
        ];
        $inEntry = true;
        continue;
    }

    if ($inEntry) {
        if (strpos($line, 'IP ADDRESS:') === 0) {
            $currentEntry['ip'] = str_replace('IP ADDRESS: ', '', $line);
        }
        if (strpos($line, 'USER AGENT:') === 0) {
            $currentEntry['user_agent'] = str_replace('USER AGENT: ', '', $line);
        }
        if (strpos($line, 'HEADERS:') === 0) {
            $inEntry = false;
        }
    }
}

// Don't forget the last entry
if (!empty($currentEntry)) {
    $entries[] = $currentEntry;
}

// --- 1. Summarize Request Patterns ---
echo "=== REQUEST SUMMARY ===\n\n";

// Count by IP
$ipCounts = [];
foreach ($entries as $entry) {
    if ($entry['ip']) {
        $ipCounts[$entry['ip']] = ($ipCounts[$entry['ip']] ?? 0) + 1;
    }
}

echo "Request Frequency by IP:\n";
arsort($ipCounts);
foreach ($ipCounts as $ip => $count) {
    echo "  $ip: $count requests\n";
}

// Count by User Agent
$agentCounts = [];
foreach ($entries as $entry) {
    $agent = $entry['user_agent'] ?? 'Unknown';
    $agentCounts[$agent] = ($agentCounts[$agent] ?? 0) + 1;
}

echo "\nRequest Frequency by User Agent:\n";
foreach ($agentCounts as $agent => $count) {
    echo "  $agent: $count requests\n";
}

// --- 2. Detect Anomalies ---
echo "\n=== ANOMALY DETECTION ===\n\n";

// High frequency IPs (> 5 requests)
echo "High Frequency IPs (> 5 requests):\n";
$highFreq = 0;
foreach ($ipCounts as $ip => $count) {
    if ($count > 5) {
        echo "  ⚠️  $ip: $count requests\n";
        $highFreq++;
    }
}
if ($highFreq === 0) echo "  None detected\n";

// Malformed user agents (empty or suspicious patterns)
echo "\nSuspicious User Agents:\n";
$suspiciousUA = ['/bot/i', '/crawler/i', '/spider/i', '/^$/'];
$uaFound = 0;
foreach ($entries as $i => $entry) {
    if (empty($entry['user_agent'])) {
        echo "  ⚠️  Entry #$i: Empty User Agent\n";
        $uaFound++;
    }
}
if ($uaFound === 0) echo "  None detected\n";

// Summary Statistics
echo "\n=== STATISTICS ===\n";
echo "Total requests logged: " . count($entries) . "\n";
echo "Unique IPs: " . count($ipCounts) . "\n";
echo "Unique User Agents: " . count($agentCounts) . "\n";

// Generate simple HTML report
$html = "<!DOCTYPE html>
<html>
<head><title>Log Analysis Report</title>
<style>
    body { font-family: sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #444; color: white; }
    .anomaly { background: #ffeeee; }
</style>
</head>
<body>
<h1>Log Analysis Report</h1>
<h2>Summary</h2>
<table>
    <tr><th>Metric</th><th>Value</th></tr>
    <tr><td>Total Requests</td><td>" . count($entries) . "</td></tr>
    <tr><td>Unique IPs</td><td>" . count($ipCounts) . "</td></tr>
    <tr><td>Unique User Agents</td><td>" . count($agentCounts) . "</td></tr>
</table>
<h2>Top IPs</h2>
<table>
    <tr><th>IP Address</th><th>Request Count</th></tr>";

foreach (array_slice($ipCounts, 0, 10) as $ip => $count) {
    $class = ($count > 5) ? 'anomaly' : '';
    $html .= "<tr class='$class'><td>$ip</td><td>$count</td></tr>";
}

$html .= "</table>
</body>
</html>";

file_put_contents(__DIR__ . '/analysis_report.html', $html);
echo "\nHTML report saved to: analysis_report.html\n";

?>