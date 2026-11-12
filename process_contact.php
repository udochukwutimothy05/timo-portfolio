<?php
/**
 * Handles the contact form.
 * - JavaScript on: replies with JSON (the page shows the result without reloading).
 * - JavaScript off: saves a flash message and redirects back to the form.
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

function respond(bool $isAjax, bool $ok, string $message, array $old = []): void
{
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $ok, 'message' => $message]);
        exit;
    }
    set_flash($ok ? 'success' : 'danger', $message);
    if (!$ok && $old) {
        $_SESSION['old'] = $old;   // keep what the person typed
    }
    redirect('contact.php');
}

// Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact.php');
}

// Honeypot: bots fill the hidden field. Pretend it worked, save nothing.
if (!empty($_POST['website'])) {
    respond($isAjax, true, 'Thank you! Your message has been sent.');
}

// CSRF check
if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    respond($isAjax, false, 'Your session expired. Please refresh the page and try again.');
}

// Read and clean the input
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$old     = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];

// Simple rate limit: one message per 30 seconds per visitor
if (!empty($_SESSION['last_message_at']) && (time() - $_SESSION['last_message_at']) < 30) {
    respond($isAjax, false, 'Please wait a moment before sending another message.', $old);
}

// Validate
$nameLen = mb_strlen($name);
$msgLen  = mb_strlen($message);

if ($nameLen < 2 || $nameLen > 100) {
    respond($isAjax, false, 'Please enter your name (2 to 100 characters).', $old);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    respond($isAjax, false, 'Please enter a valid email address.', $old);
}
if (mb_strlen($subject) > 200) {
    respond($isAjax, false, 'The subject is too long (200 characters maximum).', $old);
}
if ($msgLen < 10 || $msgLen > 3000) {
    respond($isAjax, false, 'Your message must be between 10 and 3000 characters.', $old);
}

// Save
try {
    $stmt = $pdo->prepare(
        "INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)"
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject !== '' ? $subject : null,
        ':message' => $message,
    ]);
} catch (PDOException $e) {
    error_log('Contact form save failed: ' . $e->getMessage());
    respond($isAjax, false, 'Sorry, something went wrong. Please try again or message me on WhatsApp.', $old);
}

$_SESSION['last_message_at'] = time();
respond($isAjax, true, 'Thank you! Your message has been sent. I will reply soon.');