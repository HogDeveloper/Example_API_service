ARG FPM_VERSION

FROM php:${FPM_VERSION}

WORKDIR /var/www/service_name

RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libpng-dev \
    zlib1g-dev \
    libz-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    git \
    && /usr/local/bin/docker-php-ext-install -j$(nproc) mysqli pdo \
    && docker-php-ext-enable mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer