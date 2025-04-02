# Utiliser PHP 8.3 CLI sur Alpine
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-cli-alpine AS base

# Arguments pour l'utilisateur et le groupe (peut être passé depuis docker-compose)
ARG WWWUSER=1000
ARG WWWGROUP=1000

# Variables d'environnement
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp \
    PHP_MEMORY_LIMIT=512M \
    PHP_UPLOAD_MAX_FILESIZE=100M \
    PHP_POST_MAX_SIZE=100M

# Dépendances système nécessaires
# Ajouter postgresql-dev pour pdo_pgsql, libpq pour la librairie cliente postgresql
# Ajouter linux-headers pour Swoole si nécessaire sur certaines versions d'Alpine
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libzip-dev \
    libpq \
    postgresql-dev \
    supervisor \
    build-base \
    autoconf \
    linux-headers \
    $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

# Installer les extensions PHP
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    ctype \
    fileinfo \
    mbstring \
    pdo \
    pdo_pgsql \
    sockets \
    tokenizer \
    xml \
    zip \
    opcache \
    pcntl

# Installer l'extension Redis
RUN pecl install redis && docker-php-ext-enable redis

# Installer l'extension Swoole
# Vous pouvez ajuster la version si nécessaire
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
    pecl install swoole; \
    docker-php-ext-enable swoole; \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --no-cache --virtual .swoole-phpext-rundeps $runDeps; \
    apk del .build-deps

# --- Alternative pour RoadRunner (à la place de Swoole) ---
# 1. Ne pas installer l'extension PECL Swoole ci-dessus
# 2. S'assurer que `spiral/roadrunner-cli` est dans composer.json (dev)
# 3. Ajouter une étape pour télécharger le binaire RR après composer install:
#    COPY --from=composer /app/vendor/bin/rr /usr/local/bin/rr
#    RUN chmod +x /usr/local/bin/rr && rr get-binary -y --location /usr/local/bin
# ---------------------------------------------------------

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer un utilisateur non-root
RUN addgroup -g ${WWWGROUP} www-data \
    && adduser -u ${WWWUSER} -G www-data -s /bin/sh -D www-data

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances et installer
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist
# Copier le reste de l'application
COPY --chown=www-data:www-data . .
# Générer l'autoloader optimisé et découvrir les packages
# RUN composer dump-autoload --optimize \
#     && composer run-script post-root-package-install \
#     && composer run-script post-autoload-dump \
#     && php artisan storage:link \
#     && php artisan optimize:clear

RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction
RUN php artisan storage:link 
RUN php artisan optimize:clear

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

# Copier la configuration Supervisor (si utilisée)
COPY .docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Exposer le port par défaut d'Octane
EXPOSE 8000

# Changer d'utilisateur
USER www-data

# Point d'entrée ou Commande
# Option 1: Utiliser Supervisor (recommandé pour la gestion)
# ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Option 2: Lancer Octane directement (plus simple)
# Assurez-vous que Octane est installé (`composer require laravel/octane`)
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--workers=auto", "--task-workers=auto"]
# Ou pour RoadRunner:
# CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000", "--workers=auto"]