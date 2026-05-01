FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zip unzip git curl

RUN docker-php-ext-install pdo pdo_mysql mysqli gd zip

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html