#!/bin/bash
set -e

echo "======================================"
echo "Starting deployment..."
echo "======================================"

# Parse DATABASE_URL if present (Railway)
if [ ! -z "$DATABASE_URL" ]; then
    echo "✓ DATABASE_URL detected, parsing..."

    # Parse the DATABASE_URL
    # Format: mysql://user:password@host:port/database
    PROTOCOL=$(echo $DATABASE_URL | grep :// | sed -e's,^\(.*://\).*,\1,g')
    URL_NO_PROTOCOL=$(echo ${DATABASE_URL/$PROTOCOL/})

    # Extract user and password
    USERPASS=$(echo $URL_NO_PROTOCOL | grep @ | cut -d@ -f1)
    DB_USER=$(echo $USERPASS | cut -d: -f1)
    DB_PASS=$(echo $USERPASS | cut -d: -f2)

    # Extract host, port and database
    HOSTPORT=$(echo $URL_NO_PROTOCOL | sed -e s,$USERPASS@,,g | cut -d/ -f1)
    DB_HOST=$(echo $HOSTPORT | cut -d: -f1)
    DB_PORT=$(echo $HOSTPORT | cut -d: -f2)
    DB_NAME=$(echo $URL_NO_PROTOCOL | grep / | cut -d/ -f2- | cut -d? -f1)

    # Use default port if not specified
    if [ "$DB_PORT" == "$DB_HOST" ]; then
        DB_PORT="3306"
    fi

    echo "  Host: $DB_HOST"
    echo "  Port: $DB_PORT"
    echo "  Database: $DB_NAME"
    echo "  User: $DB_USER"
    echo ""
else
    echo "✓ Using local .env configuration"
    # Source from .env file if it exists
    if [ -f .env ]; then
        export $(cat .env | grep -v '^#' | xargs)
        DB_HOST=${DB_HOST:-127.0.0.1}
        DB_NAME=${DB_NAME:-webshop_edv}
        DB_USER=${DB_USER:-webshop}
        DB_PASS=${DB_PASS:-webshop}
    else
        echo "⚠️  Warning: No .env file found, using defaults"
        DB_HOST="127.0.0.1"
        DB_NAME="webshop_edv"
        DB_USER="webshop"
        DB_PASS="webshop"
    fi
fi

# Wait for database to be ready (with retries for Railway sleeping DB)
echo "======================================"
echo "Waiting for MySQL database..."
echo "Note: Free tier DB may be sleeping and can take 30-60 seconds to wake up"
echo "======================================"

MAX_RETRIES=60
RETRY_COUNT=0
DB_READY=false

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" -e "SELECT 1" > /dev/null 2>&1; then
        DB_READY=true
        break
    fi

    RETRY_COUNT=$((RETRY_COUNT + 1))

    # Show progress every 5 attempts
    if [ $((RETRY_COUNT % 5)) -eq 0 ]; then
        echo "⏳ Attempt $RETRY_COUNT/$MAX_RETRIES: Still waiting for database to wake up..."
    fi

    sleep 2
done

if [ "$DB_READY" = false ]; then
    echo "❌ Database connection failed after $MAX_RETRIES attempts"
    echo "Please check your Railway MySQL service status"
    exit 1
fi

echo ""
echo "✓ Database connection successful!"
echo ""

# Run database setup
echo "======================================"
echo "Running database setup..."
echo "======================================"
php Model/setup.php || echo "⚠️  Setup already completed or failed (this is often OK)"

echo ""
echo "======================================"
echo "Starting Apache web server..."
echo "======================================"
exec apache2-foreground
