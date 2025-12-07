# Use PHP 8.3 with Apache
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Configure Apache DocumentRoot to point to View directory
RUN sed -i 's|/var/www/html|/var/www/html/View|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/html|/var/www/html/View|g' /etc/apache2/apache2.conf

# Update Directory permissions in Apache config to allow .htaccess
RUN echo '<Directory /var/www/html/View>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create logs directory
RUN mkdir -p /var/www/html/logs && chown -R www-data:www-data /var/www/html/logs

# Make entrypoint script executable
RUN chmod +x /var/www/html/railway-entrypoint.sh

# Expose port (Railway will set PORT env variable)
EXPOSE 80

# Use custom entrypoint that runs database setup
CMD ["/var/www/html/railway-entrypoint.sh"]
