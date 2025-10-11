#!/bin/bash
set -e

if [ ! -f "vendor/autoload.php" ]; then
	composer install --no-progress --no-interaction
fi

if [ "$CONTAINER_ROLE" = "app" ]; then
    php artisan migrate
    php artisan key:generate
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    exec php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
elif [ "$CONTAINER_ROLE" = "queue" ]; then
    exec php artisan queue:work --verbose --tries=3 --timeout=90
elif [ "$CONTAINER_ROLE" = "scheduler" ]; then
    exec sh -c "while [ true ]; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
else
    echo "Could not match the container role \"$CONTAINER_ROLE\""
    exit 1
fi
