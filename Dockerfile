FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libssl-dev \
    git \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install redis mongodb \
    && docker-php-ext-enable redis mongodb

RUN a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80