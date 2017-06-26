#!/usr/local/bin/php
php artisan migrate:reset
php artisan migrate
php artisan db:seed