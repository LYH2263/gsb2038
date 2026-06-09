FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev \
    && docker-php-ext-install zip pdo_mysql mbstring pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
EXPOSE 8000
