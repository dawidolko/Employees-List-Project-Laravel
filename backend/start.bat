call composer install

@REM call composer update

call composer require laravel/sanctum

php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"

call php artisan key:generate

call php artisan storage:link

call php artisan migrate

@REM call php artisan db:seed

@REM call php artisan migrate:fresh --seed

call php artisan serve

start http://127.0.0.1:8000

code .
