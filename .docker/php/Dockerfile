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
# Ajout de dépendances pour extensions courantes et Swoole/PgSQL
# Suppression des doublons
RUN apk update && apk add --no-cache \
    # PHP Core & Extensions Deps
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    libxslt-dev \
    curl-dev \
    freetype-dev \
    icu-dev \
    gettext-dev \
    libpq-dev \      # Pour pdo_pgsql
    libsodium-dev \
    gmp-dev \
    oniguruma-dev \   # Pour mbstring
    # Swoole Deps
    linux-headers \
    # Build tools (peuvent être supprimés dans une étape ultérieure si nécessaire)
    autoconf \
    build-base \
    pkgconfig \
    # Client libs et Utilitaires
    libpq \          # Client PostgreSQL runtime
    bash \
    git \
    zip \
    unzip \
    curl \
    wget \
    openssl \
    supervisor \
    $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

# Installer les extensions PHP
# Suppression des doublons commentés
RUN docker-php-ext-install -j$(nproc) \
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

# Installer l'extension Redis
RUN pecl install redis && docker-php-ext-enable redis

# Installer l'extension Swoole
# Note: Le nettoyage des dépendances de build est plus efficace s'il est fait après toutes les installations PECL/ext-install
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers; \
    pecl install swoole; \
    docker-php-ext-enable swoole; \
    # runDeps="$( \
    #     scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
    #         | tr ',' '\n' \
    #         | sort -u \
    #         | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    # )"; \
    # apk add --no-cache --virtual .swoole-phpext-rundeps $runDeps; \
    # La détection automatique des dépendances runtime peut être complexe, on garde les libs nécessaires explicitement
    apk del .build-deps

# --- Alternative pour RoadRunner ---
# Voir les commentaires dans le Dockerfile original

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer un utilisateur non-root (CORRIGÉ)
RUN delgroup www-data || true \
    && deluser www-data || true \
    && addgroup -g ${WWWGROUP} www-data \
    && adduser -u ${WWWUSER} -G www-data -s /bin/sh -D www-data

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de dépendances et installer les dépendances (cache layer)
COPY --chown=www-data:www-data composer.json composer.lock ./
# Installer uniquement les dépendances sans scripts/autoload pour le cache
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copier le reste de l'application
COPY --chown=www-data:www-data . .

# Générer l'autoloader optimisé, exécuter les scripts composer et commandes artisan
# Combinaison des deux RUN composer pour plus d'efficacité
RUN composer install --prefer-dist --optimize-autoloader --no-dev --no-interaction \
    && php artisan storage:link \
    && php artisan optimize:clear

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
# Option 1: Utiliser Supervisor
# ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"] # -n pour tourner au premier plan

# Option 2: Lancer Octane directement (par défaut ici)
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--workers=auto", "--task-workers=auto"]
# Ou pour RoadRunner:
# CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000", "--workers=auto"]