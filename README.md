<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Rbroker bot

## Getting Started
1. remote and pull the latest version of the source code on your host
> git remote add your_name _https://github.com/FrigoreD/Rbroker-bot.git_

> git pull your_name
2. install with conposer
> composer install --optimize-autoloader --no-dev
3. make and edit .env file's lines
>mv .env.example .env

You need to change these lines
>DB_DATABASE=your_db_name

>DB_USERNAME=your_db_login

>DB_PASSWORD=your_db_password
4. set key and migrate tables
> php artisan key:generate
php artisan migrate
5. for deploy you can also use these
> php artisan config:cache

>php artisan route:cache

>php artisan view:cache

>php artisan storage:link
6. set webhook like this: 
>//https://api.telegram.org/botYOUR_TOKEN/setwebhook?url=YOUR_HOST_URL/webhook

## Documentation

I use laravel because it makes it easy to work with the database through the model. You can easily migrate tables, build the architecture and expand the functionality.

### Model
model helps me to communicate with database and do it not direct but to MVC pattern

<a href="https://imgbb.com/"><img src="https://i.ibb.co/g7TYDnd/1.png" alt="1" border="0"></a>

### Migrations
You can type your own table in method up with Schemma:create of Facades
it's simple to make new attributes and migrate in DB
It's usually test with Seeders to fill DB with some test values
<a href="https://ibb.co/gwLzJjf"><img src="https://i.ibb.co/Cv3hQ1Y/2.png" alt="2" border="0"></a>

### Bot
Also check all comments in App\Htpp\Controllers\TelegramUserController.php and App\Helpers\Telegram.php


