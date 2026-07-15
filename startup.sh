#!/bin/sh
# Azure App Service startup script.
# Set the App Service "Startup Command" to: bash /home/site/wwwroot/startup.sh

# Point nginx at Laravel's public/ directory
cp /home/site/wwwroot/default /etc/nginx/sites-enabled/default
service nginx reload

cd /home/site/wwwroot

# ponytail: migrate on every boot; idempotent, cheap for this schema size
php artisan migrate --force || true
php artisan db:seed --class=AdminSeeder --force || true
php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
