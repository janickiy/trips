#!/usr/bin/env bash

set -e
role=$1

telegram_token=${DEVOPSBOT_TOKEN}
telegram_channel="-593998594"
release=$(cat /app/version.txt)


trap "notify_error ${role}" ERR

notify() {
    /app/docker/telegram.sh -t ${telegram_token} -c ${telegram_channel} -T "$1 STARTED" -M "${release}"
}

notify_error() {
    /app/docker/telegram.sh -t ${telegram_token} -c ${telegram_channel} -T "$1 ERROR" -M "${release}"
}


echo "Cache..."
php artisan optimize

if [ "$role" = "worker" ]; then

    echo "Running queue worker..."
    sleep 10
    notify $role
    exec php /app/artisan queue:work --sleep=3 --tries=3 --timeout=90

elif [ "$role" = "websocket" ]; then

    echo "Running websocket service..."
    sleep 15
    notify $role
    exec php /app/artisan websockets:serve

elif [ "$role" = "api" ]; then

    echo "Running migrations..."
    php artisan migrate --force
    echo "Running php-fpm+nginx api service..."
    notify $role
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

else
    echo "Container role not specified"
    exit 1
fi
