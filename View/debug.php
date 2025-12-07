<?php
// Debug script to check database configuration
// DELETE THIS FILE after deployment is working!

header('Content-Type: text/plain');

echo "=== Railway Database Debug ===\n\n";

// Check if DATABASE_URL exists
$databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
echo "DATABASE_URL exists: " . ($databaseUrl ? "YES" : "NO") . "\n";

if ($databaseUrl) {
    // Show sanitized version (hide password)
    $sanitized = preg_replace('/\/\/([^:]+):([^@]+)@/', '//\1:****@', $databaseUrl);
    echo "DATABASE_URL (sanitized): " . $sanitized . "\n\n";

    // Parse it
    $db = parse_url($databaseUrl);
    echo "Parsed components:\n";
    echo "  - Host: " . ($db['host'] ?? 'NOT SET') . "\n";
    echo "  - Port: " . ($db['port'] ?? 'NOT SET') . "\n";
    echo "  - User: " . ($db['user'] ?? 'NOT SET') . "\n";
    echo "  - Pass: " . (isset($db['pass']) ? "****" : 'NOT SET') . "\n";
    echo "  - DB Name: " . (isset($db['path']) ? ltrim($db['path'], '/') : 'NOT SET') . "\n\n";
}

// Load config and show what it parsed
require_once __DIR__ . '/../Controller/config.php';

echo "Config constants:\n";
echo "  - DB_HOST: " . DB_HOST . "\n";
echo "  - DB_NAME: " . DB_NAME . "\n";
echo "  - DB_USER: " . DB_USER . "\n";
echo "  - DB_PASS: " . (DB_PASS ? "****" : 'EMPTY') . "\n\n";

// Try to connect
echo "Attempting database connection...\n";
try {
    $testPdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ SUCCESS: Database connection works!\n";

    // Test query
    $result = $testPdo->query("SELECT COUNT(*) as count FROM products")->fetch();
    echo "✓ Products table exists with " . $result['count'] . " products\n";

} catch (PDOException $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}

echo "\n=== Debug Complete ===\n";
echo "DELETE /View/debug.php after fixing the issue!\n";
