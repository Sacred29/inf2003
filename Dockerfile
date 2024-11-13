# Use the official PHP image with Apache
FROM php:8.2-apache

# Install dependencies for MongoDB PHP extension with SSL support
RUN apt-get update && apt-get install -y \
    unzip \
    pkg-config \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Copy application files to the Apache document root
COPY src/ /var/www/html/


RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for HTTP traffic
EXPOSE 80
