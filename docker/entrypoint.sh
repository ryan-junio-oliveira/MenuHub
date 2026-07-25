#!/bin/sh
set -e

cd /var/www

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    php artisan migrate --force
    php artisan db:seed --class=PlanSeeder --force
fi

php artisan storage:link --force
php artisan optimize

exec /usr/bin/supervisord -c /etc/supervisord.conf
