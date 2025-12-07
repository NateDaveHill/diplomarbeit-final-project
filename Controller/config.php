<?php

function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if(!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load environment variables from .env file
$envPath = dirname(__DIR__) . '/.env';
loadEnv($envPath);

// Database configuration
// Railway provides DATABASE_URL, parse it if available
$databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

if ($databaseUrl) {
    $db = parse_url($databaseUrl);
    $dbHost = $db['host'] ?? 'localhost';
    // Add port if specified
    if (isset($db['port'])) {
        $dbHost .= ':' . $db['port'];
    }
    define('DB_HOST', $dbHost);
    define('DB_NAME', ltrim($db['path'] ?? '/webshop_edv', '/'));
    define('DB_USER', $db['user'] ?? 'root');
    define('DB_PASS', $db['pass'] ?? '');
} else {
    define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'webshop_edv');
    define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root');
    define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');
}


//Application configuration
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'true') === 'true');

// Database connection
try{
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
    } else {
        die("Datenbankverbindung fehlgeschlagen. Bitte kontaktieren Sie den Administrator.");
    }
}

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

// Check if we're on HTTPS (works with Railway's proxy setup)
$isHttps = (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
);

if ($isHttps) {
    ini_set('session.cookie_secure', 1);
}

session_name($_ENV['SESSION_NAME'] ?? 'webshop_session');
session_start();

// config functions

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isPremium()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'premium';
}

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function logError($message, $context = [])
{
    if (APP_DEBUG) {
        $logMessage = date('Y-m-d H:i:s') . " - " . $message;
        if (!empty($context)) {
            $logMessage .= ' | Context: ' . json_encode($context);
        }
        error_log($logMessage . PHP_EOL, 3, __DIR__ . '/../logs/error.log');
    }
}

function redirect($url)
{
    header("Location: $url");
    exit;
}