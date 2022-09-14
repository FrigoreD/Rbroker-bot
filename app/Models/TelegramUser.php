<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Our Telegram model for communication with DB
 */
class TelegramUser extends Model
{
    use HasFactory;

    public $timestamps = false;
}
