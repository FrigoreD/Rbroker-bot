<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramUserController;




/**Our main view to connect with telegram bot
webhook can be set like this:
//https://api.telegram.org/botYOUR_TOKEN/setwebhook?url=YOUR_HOST_URL/webhook
don't forget to do it, otherwise nothing will work
*/
Route::post('/webhook', [TelegramUserController::class, 'index'])->name('TelegramBot');
