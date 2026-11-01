# Use PHP 8.3 FPM image
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev \
    libxml2-dev zip unzip npm wget tar \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Make entrypoint executable
RUN chmod +x /usr/local/bin/docker-php-entrypoint /var/www/docker/entrypoint.sh

EXPOSE 9000
CMD ["/var/www/docker/entrypoint.sh"]
