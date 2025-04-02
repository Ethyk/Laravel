FROM php:8.2-fpm-alpine AS build

# Installer les dépendances système nécessaires
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    freetype-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    supervisor

# Installer les extensions PHP nécessaires
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql sockets mbstring gd bcmath intl pcntl && \
    pecl install openswoole && docker-php-ext-enable openswoole

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Préparer le répertoire de l'application
WORKDIR /app
COPY . .

# Installer les dépendances PHP et Node.js
RUN composer install --no-dev --optimize-autoloader && npm install

# Configurer Laravel Octane avec OpenSwoole
RUN composer require laravel/octane && php artisan octane:install --server="swoole"

# Nettoyer les caches Laravel
RUN php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Exposer le port utilisé par Octane
EXPOSE 8000

# Commande de démarrage pour Laravel Octane avec OpenSwoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
