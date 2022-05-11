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


echo "Running migrations..."
php artisan migrate --force

echo "Cache..."
php artisan optimize

if [ "$role" = "admin" ]; then

    echo "Running php-fpm+nginx api service..."
    notify $role
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

else
    echo "Container role not specified"
    exit 1
fi
