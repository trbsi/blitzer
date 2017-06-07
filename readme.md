#reset all migrations and drop tables
php artisan migrate:reset

#migrate everything
php artisan migrate
php artisan db:seed