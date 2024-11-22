FROM php:8.2-apache


RUN apt-get update && apt-get install -y \
    unzip \
    pkg-config \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb


COPY src/ /var/www/html/


RUN chown -R www-data:www-data /var/www/html


EXPOSE 80
