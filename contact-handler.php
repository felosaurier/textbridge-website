<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* ================= CONFIG ================= */
const RECIPIENT_EMAIL = 'team@textbridge.at';

const SMTP_HOST   = 'mail.textbridge.at';
const SMTP_PORT   = 465;
const SMTP_SECURE = 'ssl';

const SMTP_USER = 'no-reply@textbridge.at';
const SMTP_PASS = 'team@textbridge2027'; // NICHT das alte
/* ========================================== */

function respond(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['success' => $code === 200, 'message' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

/* --- Basic checks --- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, 'Nur POST erlaubt.');
}

if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    respond(400, 'Sicherheitspr端fung fehlgeschlagen.');
}

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    respond(400, 'Ung端ltige Anfrage.');
}

/* --- Sanitize --- */
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    respond(400, 'Bitte alle Felder ausf端llen.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, 'Ung端ltige E-Mail-Adresse.');
}

/* --- Send mail --- */
try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_USER, 'TextBridge Website');
    $mail->addAddress(RECIPIENT_EMAIL);
    $mail->addReplyTo($email, $name);

    $mail->Subject = '[TextBridge Kontakt] ' . $subject;
    $mail->Body =
        "Name: $name\n" .
        "E-Mail: $email\n\n" .
        "Nachricht:\n$message\n";

    $mail->send();
    respond(200, 'Nachricht erfolgreich gesendet.');
} catch (Exception $e) {
    error_log('PHPMailer Helloly Error: ' . $e->getMessage());
    respond(500, 'Mailversand fehlgeschlagen.');
}
