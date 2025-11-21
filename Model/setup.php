<?php

// Load env variables
function loadEnv($path) {
    if(!file_exists($path)) {
        return false;
    }

    $lines + file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $value)) {
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load .env file from project root
$envPath = dirname(__DIR__) . '/.env';
if (!loadEnv($envPath)) {
    die("Error: .env file not found at $envPath. Please copy .env.example to .env and configure it.\n");
}

$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'webshop_edv';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';

echo "Database Setup Configuration:\n";
echo "  Host: $db_host\n";
echo "  Database: $db_name\n";
echo "  User: $db_user\n";
echo "  Password: " . (empty($db_pass) ? "(empty)" : "***") . "\n\n";

// Step 1: Connect without database to create it
echo "[1/4] Connecting to MySQL server...\n";
try {
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Step 2: Create database if not exists
echo "[2/4] Creating database '$db_name' if it does not exist...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "✓ Database checked/created successfully\n\n";
} catch (PDOException $e) {
    die("Database creation failed: " . $e->getMessage() . "\n");
}

// Step 3: Connect to the newly created database
echo "[3/4] Creating tables...\n";
$pdo->exec("USER `$db_name`");
