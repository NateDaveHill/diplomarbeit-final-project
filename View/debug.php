<?php
// Debug script to check database configuration on Railway
// ⚠️ DELETE THIS FILE after deployment is working!

header('Content-Type: text/plain');

echo "=== Railway Database Debug Info ===\n\n";

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

echo "Config constants after parsing:\n";
echo "  - DB_HOST: " . DB_HOST . "\n";
echo "  - DB_NAME: " . DB_NAME . "\n";
echo "  - DB_USER: " . DB_USER . "\n";
echo "  - DB_PASS: " . (DB_PASS ? "****" : 'EMPTY') . "\n";
echo "  - APP_ENV: " . APP_ENV . "\n";
echo "  - APP_DEBUG: " . (APP_DEBUG ? 'true' : 'false') . "\n\n";

// Check if PDO connection was established
echo "PDO Connection status:\n";
if (isset($pdo) && $pdo instanceof PDO) {
    echo "✓ PDO connection object exists!\n";

    // Try a test query
    try {
        $result = $pdo->query("SELECT COUNT(*) as count FROM products")->fetch();
        echo "✓ Products table exists with " . $result['count'] . " products\n";

        $userCount = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
        echo "✓ Users table exists with " . $userCount['count'] . " users\n";

        $orderCount = $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch();
        echo "✓ Orders table exists with " . $orderCount['count'] . " orders\n";

        echo "\n✓✓✓ SUCCESS: Database is fully working! ✓✓✓\n";

    } catch (PDOException $e) {
        echo "✗ Query error: " . $e->getMessage() . "\n";
        echo "  This might mean tables don't exist yet. Run setup.php\n";
    }
} else {
    echo "✗ PDO connection failed or not established\n";
}

echo "\n=== Debug Complete ===\n";
echo "⚠️ IMPORTANT: Delete /View/debug.php after fixing!\n";
