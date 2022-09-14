<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Rbroker bot

## Getting Started
1. remote and pull the latest version of the source code on your host
> git remote add your_name _https://github.com/FrigoreD/Rbroker-bot.git_
> git pull your_name
2. install with conposer
> composer install --optimize-autoloader --no-dev
3. edit .env file's lines
>DB_DATABASE=your_db_name
DB_USERNAME=your_db_login
DB_PASSWORD=your_db_password
4. set key and migrate tables
> php artisan key:generate
php artisan migrate
5. for deploy you can also use these
> php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
6. done




