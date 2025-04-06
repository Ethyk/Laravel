#!/bin/sh
set -e

container_mode=${CONTAINER_MODE:-"http"}


# Démarrer Octane
php artisan octane:start --server=swoole --host=0.0.0.0 --watch &

# Démarrer le watcher frontend
# bun run dev &

if [ "${container_mode}" = "http" ]; then
    bun run dev &
fi

# Surveiller les fichiers PHP/JS
while inotifywait -qr -e modify,create,delete \
    app/ config/ routes/ database/ resources/ vite.config.js; do
    
    if echo "$@" | grep -q '\.php$'; then
        php artisan octane:reload
    elif [ "${container_mode}" = "http" ]; then
        bun run build
    fi
done
