FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    git \
    openssl \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd zip \
    && echo "short_open_tag=On" > /usr/local/etc/php/conf.d/legacy.ini \
    && echo "display_errors=On" >> /usr/local/etc/php/conf.d/legacy.ini \
    && echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/legacy.ini \
    && a2enmod rewrite ssl headers \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html