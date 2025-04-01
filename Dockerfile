FROM php:8.3-fpm-alpine

# Install system dependencies including build tools
RUN apk update && apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    libxslt-dev \
    curl-dev \
    freetype-dev \
    icu-dev \
    gettext-dev \
    libpq-dev \
    libsodium-dev \
    gmp-dev \
    oniguruma-dev \
    bash \
    git \
    zip \
    unzip \
    curl \
    wget \
    openssl \
    supervisor \
    linux-headers \
    autoconf \
    build-base \
    pkgconfig \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    mbstring \
    opcache \
    sockets \
    soap \
    xsl \
    gmp \
    sodium

# Install PECL extensions
RUN pecl install openswoole redis && \
    docker-php-ext-enable openswoole redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure PHP for Octane
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.jit_buffer_size=100M" >> /usr/local/etc/php/conf.d/opcache.ini

# Prepare application directory
RUN mkdir -p /app/storage /app/bootstrap/cache && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage /app/bootstrap/cache

# Set working directory
WORKDIR /app

# Copy application files first
COPY . .

# Install dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Expose ports (9000 for FPM, 8000 for Octane)
EXPOSE 9000 8000

# Configure Octane command
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]

# Environment variables
ENV COMPOSER_HOME=/app/composer \
    COMPOSER_CACHE_DIR=/app/composer/cache \
    PHP_FPM_USER=www-data \
    PHP_FPM_GROUP=www-data \
    PHP_MEMORY_LIMIT=256M \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=101M \
    OCTANE_SERVER=swoole
