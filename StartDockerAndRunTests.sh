#!/bin/sh

mv .env .env-bak
cp .env.docker .env

docker-compose up -d

sh ./web.sh composer install --no-interaction --prefer-dist --no-suggest
sh ./web.sh php artisan key:generate
sh ./web.sh php artisan migrate
sh ./web.sh php artisan passport:install
sh ./web.sh ./vendor/bin/phpunit --configuration phpunit.xml.dist

mv .env-bak .env
