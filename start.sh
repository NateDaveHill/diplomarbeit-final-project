#!/bin/bash
set -e

echo "Starting Railway deployment..."

# Wait for database to be ready
echo "Waiting for database connection..."
until php -r "
require_once 'Controller/config.php';
try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    echo 'Database connected!';
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "Database not ready yet, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run database setup
echo "Running database setup..."
php Model/setup.php || echo "Setup already completed or failed"

echo "Starting Apache..."
exec apache2-foreground
