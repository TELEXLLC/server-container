<?php

\$logFile = __DIR__ . '/request_log.txt';

// --- 1. Sanitize and Validate Input ---
\$input = [];
foreach ($_GET as $key => \$value) {
    $cleanKey = trim(strip_tags($key));
    $cleanValue = trim(strip_tags($value));
    if (!empty(\$cleanKey
