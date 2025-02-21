php -r "copy('.env.example', '.env');"

composer install

# composer update

composer require laravel/sanctum

composer require barryvdh/laravel-dompdf

php artisan key:generate

php artisan storage:link

# php artisan migrate

# php artisan db:seed

# php artisan migrate:fresh --seed

php artisan serve

code .
