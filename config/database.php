<?php
/**
 * Database connection (PDO)
 * Used by every page: require_once __DIR__ . '/config/database.php';
 * Then use the $pdo variable.
 */

// ---- Settings: XAMPP defaults. Change these when you deploy online. ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP's root password is empty by default
define('DB_CHARSET', 'utf8mb4');

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // throw errors we can catch
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // rows come back as arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                    // real prepared statements
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log the real error privately, show visitors a safe message.
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Sorry, the site is temporarily unavailable. Please try again later.');
}