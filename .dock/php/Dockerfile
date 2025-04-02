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
    # $PHPIZE_DEPS est implicitement ajouté par les commandes pecl/docker-php-ext-*
    && rm -rf /var/cache/apk/*

# Installer les extensions PHP
# On combine pour réduire les couches
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
    \
    docker-php-ext-install -j$(nproc) \
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
        pcntl; \
    \
    # Installer Redis
    pecl install redis; \
    docker-php-ext-enable redis; \
    \
    # Installer Swoole
    pecl install swoole; \
    docker-php-ext-enable swoole; \
    \
    # Nettoyage des dépendances de build
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
    apk add --no-cache --virtual .php-rundeps $runDeps; \
    apk del .build-deps

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer un utilisateur non-root
RUN addgroup -g ${WWWGROUP} www-data \
    && adduser -u ${WWWUSER} -G www-data -s /bin/sh -D www-data

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier uniquement les fichiers nécessaires pour l'installation des dépendances
COPY --chown=www-data:www-data composer.json composer.lock ./

# Installer les dépendances Composer
# - Utiliser --prefer-dist et --no-dev pour une image de prod/optimisée
# - --optimize-autoloader génère un autoloader optimisé
# - --no-interaction évite les questions interactives
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction

# Copier le reste du code de l'application
COPY --chown=www-data:www-data . .

# Exécuter les commandes Artisan après l'installation et la copie complète
# optimize:clear est généralement suffisant pour le dev/build.
# Pour la production, vous pourriez vouloir utiliser:
# php artisan config:cache && php artisan route:cache && php artisan view:cache
RUN php artisan optimize:clear \
    && php artisan storage:link

# Définir les permissions finales sur les dossiers nécessaires
# Important de le faire APRES la copie et les commandes artisan
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

# Copier la configuration Supervisor (si utilisée)
# Assurez-vous que ce fichier existe si vous décommentez ENTRYPOINT
# COPY .docker/php/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Exposer le port par défaut d'Octane
EXPOSE 8000

# Changer d'utilisateur pour l'exécution
USER www-data

# Point d'entrée ou Commande
# Option 1: Utiliser Supervisor (recommandé pour la gestion)
# Assurez-vous que le service [program:octane] dans supervisor.conf est correct
# ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

# Option 2: Lancer Octane directement (plus simple, utilisé ici)
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--workers=auto", "--task-workers=auto"]
# Ou pour RoadRunner:
# CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000", "--workers=auto"]