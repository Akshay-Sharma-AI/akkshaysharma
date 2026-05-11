<?php
/**
 * submit.php — single-file form handler for the static site on Hostinger basic.
 *
 * Handles three form_type values from index.html:
 *   - contact     (name, email, phone, message)
 *   - newsletter  (name, email)
 *   - booking     (name, email, phone, interest, experience_level, message)
 *
 * Each submission is:
 *   1) Appended to a CSV file in /data (created automatically)
 *   2) Emailed to $RECIPIENT_EMAIL via PHP mail()  (Hostinger ships PHP mail)
 *
 * No database needed — fits the Hostinger Premium / Single shared plan.
 *
 *  >>>  CONFIGURE  <<<
 *  Change $RECIPIENT_EMAIL below to the inbox you want submissions to land in.
 */

// ---------- CONFIG ------------------------------------------------------------
$RECIPIENT_EMAIL = 'connect@akkshaysharma.com';         // <-- change me
$SITE_NAME       = 'Akkshay Sharma — Personal Site';
$DATA_DIR        = __DIR__ . '/data';                   // CSV storage (auto-created)
// -----------------------------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');

function reply($success, $message = '', $error = '', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'error'   => $error,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reply(false, '', 'Method not allowed', 405);
}

// ----- gather + sanitize -----------------------------------------------------
function v($key) {
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

$form_type        = v('form_type');
$name             = v('name');
$email            = v('email');
$phone            = v('phone');
$message          = v('message');
$interest         = v('interest');
$experience_level = v('experience_level');

// Honeypot (optional) — if someone adds a hidden "website" field, reject.
if (!empty($_POST['website'])) {
    reply(true, 'Thanks!');   // pretend success to bots
}

// ----- per-form-type validation ----------------------------------------------
$allowed = ['contact', 'newsletter', 'booking'];
if (!in_array($form_type, $allowed, true)) {
    reply(false, '', 'Invalid form type', 400);
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    reply(false, '', 'Please provide a valid email.', 400);
}

if ($form_type === 'contact') {
    if ($name === '' || $message === '') {
        reply(false, '', 'Please fill in all required fields.', 400);
    }
} elseif ($form_type === 'booking') {
    if ($name === '') {
        reply(false, '', 'Please fill in all required fields.', 400);
    }
}
// newsletter: only email required, already validated.

// ----- write to CSV ----------------------------------------------------------
if (!is_dir($DATA_DIR)) {
    @mkdir($DATA_DIR, 0755, true);
}

// Block direct browsing of /data (Apache .htaccess)
$htaccess = $DATA_DIR . '/.htaccess';
if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Require all denied\nDeny from all\n");
}

$csvFile = $DATA_DIR . '/' . $form_type . '.csv';
$isNew   = !file_exists($csvFile);

$row = [
    date('c'),
    $name,
    $email,
    $phone,
    $interest,
    $experience_level,
    $message,
    $_SERVER['REMOTE_ADDR'] ?? '',
    $_SERVER['HTTP_USER_AGENT'] ?? '',
];
$headers = ['timestamp','name','email','phone','interest','experience_level','message','ip','user_agent'];

if (($fh = @fopen($csvFile, 'a')) !== false) {
    if ($isNew) {
        fputcsv($fh, $headers);
    }
    fputcsv($fh, $row);
    fclose($fh);
}

// ----- email notification ----------------------------------------------------
$subjectMap = [
    'contact'    => '[Contact] New message from ' . $name,
    'booking'    => '[Booking] Session request from ' . $name,
    'newsletter' => '[Newsletter] New subscriber: ' . $email,
];
$subject = $subjectMap[$form_type] ?? '[Form] New submission';

$body  = "Form: $form_type\n";
$body .= "Site: $SITE_NAME\n";
$body .= "Time: " . date('Y-m-d H:i:s') . "\n";
$body .= str_repeat('-', 40) . "\n";
$body .= "Name:             $name\n";
$body .= "Email:            $email\n";
$body .= "Phone:            $phone\n";
if ($interest)         $body .= "Interest:         $interest\n";
if ($experience_level) $body .= "Experience level: $experience_level\n";
if ($message) {
    $body .= "\nMessage:\n$message\n";
}
$body .= str_repeat('-', 40) . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n";

$headers_arr = [
    'From: '         . 'no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost'),
    'Reply-To: '     . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8',
];

@mail($RECIPIENT_EMAIL, $subject, $body, implode("\r\n", $headers_arr));

// ----- success message -------------------------------------------------------
$successMessages = [
    'contact'    => "Thanks $name! I'll get back to you shortly.",
    'booking'    => "Thanks $name — request received. I'll email you to schedule the session.",
    'newsletter' => "You're subscribed. Welcome aboard!",
];

reply(true, $successMessages[$form_type] ?? 'Thanks!');
