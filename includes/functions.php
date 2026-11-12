<?php
/**
 * Shared helpers and site settings.
 * Included by header.php, so every page gets these automatically.
 */

// ---- Secure session start ----
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

// ---- Site settings (edit these) ----
define('SITE_NAME', 'Timothy Udochukwu');
define('SITE_TAGLINE', 'Full Stack Web Developer & Instructor');
define('SITE_DESCRIPTION', 'Timothy Udochukwu is a Nigerian full stack web developer who builds websites, fixes existing ones, and teaches web development with HTML, CSS, Bootstrap, JavaScript, PHP and MySQL.');
define('SITE_EMAIL', 'udochukwutimothy05@gmail.com');
define('SITE_WHATSAPP', '2347079039946');
define('BASE_URL', '/portfolio');   // change to '' when the site goes live at the domain root
define('SITE_URL', 'https://yourdomain.com');   // your real domain when live, no trailing slash
define('APP_ENV', 'local');                     // change to 'production' on the live server

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');             // never show PHP errors to visitors
    ini_set('log_errors', '1');
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// ---- Helpers ----

/** Escape output to prevent XSS. Use for every value printed from the database or user. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Build a URL inside the site, e.g. url('assets/css/style.css') */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Redirect and stop the script. */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Turn "My First Project!" into "my-first-project" */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// ---- CSRF protection ----

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Put this inside every <form method="post"> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

// ---- Flash messages (one-time notices after a redirect) ----

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// ---- Admin authentication ----

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

/** Call at the top of every admin page. */
function require_admin(): void
{
    if (!is_admin_logged_in()) {
        redirect('admin/login.php');
    }
}

/** Returns the profile photo file name from assets/img/, or null if none exists. */
function get_profile_photo(): ?string
{
    foreach (['profile', 'Profile', 'timothy'] as $base) {
        foreach (['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG'] as $ext) {
            if (file_exists(__DIR__ . "/../assets/img/$base.$ext")) {
                return "$base.$ext";
            }
        }
    }
    return null;
}

// ---- Time zone (so dates show in Nigerian time) ----
date_default_timezone_set('Africa/Lagos');

// ---- Image uploads ----

/**
 * Validates and saves an uploaded image into /uploads.
 * Returns [true, 'newfilename.jpg'], [true, null] when no file was chosen,
 * or [false, 'error message'].
 */
function handle_image_upload(array $file): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'The image could not be uploaded. Try a smaller file.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [false, 'The image must be 2 MB or smaller.'];
    }

    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return [false, 'Only JPG, PNG or WebP images are allowed.'];
    }

    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];   // random name, never the original
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return [false, 'Could not save the image. Check the uploads folder.'];
    }
    return [true, $name];
}

/** Deletes a file from /uploads (safe: only the file name is used). */
function delete_upload(?string $filename): void
{
    if (!$filename) {
        return;
    }
    $path = __DIR__ . '/../uploads/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

/** Makes a slug from the title that no other project uses. */
function unique_slug(PDO $pdo, string $title, ?int $ignoreId = null): string
{
    $base = slugify($title);
    if ($base === '') {
        $base = 'project';
    }
    $slug = $base;
    $i = 2;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE slug = :slug AND id != :id");
    while (true) {
        $stmt->execute([':slug' => $slug, ':id' => $ignoreId ?? 0]);
        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

/** True if the value is empty or a valid http(s) URL. */
function is_valid_http_url(string $value): bool
{
    return $value === '' || (filter_var($value, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $value));
}