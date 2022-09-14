<?php

//https://api.telegram.org/bot5437694071:AAEg7SrNvfiglrK6z9FIxQ5uHnw24j6lMAo/setwebhook?url=https://www.rbroker-bot.ru/webhook
namespace App\Http\Controllers;

use App\Helpers\Telegram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TelegramUser;


class TelegramUserController extends Controller
{
    public function index(Request $request, Telegram $telegram){
        $message = $telegram->text($request);
        switch ($message){
            case '/help':
                $text = "Список полезных функций:\n/add datetime; name; phone - Добавление новой строки\n/del id - удаление строки\n/sel id - Вывести содержимое строки";
                break;
            case (bool)preg_match('/\/add\s(.*);\s(.*);\s(.*)/', $message, $res):
                try{
                    if(!$phone = $this->nameMatch($res[2])){
                        $text = 'Неверно введено имя';
                        break;
                    }
                    if(!$phone = $this->phoneMatch($res[3])){
                        $text = 'Неверно введён номер телефона';
                        break;
                    }
                    if(!$date = $this->setDate($res[1])){
                        $text = 'Неверно введена дата';
                        break;
                    }
                    $this->newLine($date, $phone, $phone);
                    $text = 'Добавлена запись: '. $res[1] . ' ' . $phone . ' ' . $phone;
                }catch (\Throwable $e){
                        $text = 'Пользователь с таким номером телефона уже существует или неверно введена дата';
                }
                break;
            case (bool)preg_match('/\/del\s(.*)/', $message, $res):
                $id = $res[1];
                try {
                    $data = $this->getEntryFromDb($id);
                    TelegramUser::query()->find($id)->delete();
                    $text = 'Строка ' . $data . ' Была удалена';
                }catch (\Throwable $e){
                    $text = 'Строки с id: ' . $id . ' не существует';
                }
                break;
            case (bool)preg_match('/\/sel\s(.*)/', $message, $res):
                $id = $res[1];
                try{
                    $text = $this->getEntryFromDb($id);
                }catch (\Throwable $e){
                    $text = 'Строки с id: ' . $id . ' не существует';
                }
                break;
            default:
                $text = 'Нет такой команды';
        }
        $telegram->sendMessage($request, $text);
    }

    protected function setDate($date){
        $formats = ['Y-m-d','d-m-Y', 'Y/m/d', 'd/m/Y', 'Y m d', 'd m Y', 'd.m.Y', 'Y.m.d'];
        foreach ($formats as $format) {
            try{
               return Carbon::createFromFormat($format, $date) -> timestamp;
            } catch (\Throwable $e) {
            }
        }
    }
    protected function getDate($date){
        return Carbon::createFromTimestamp($date)->format('d.m.y h:m');
    }
    protected function newLine($datetime, $name, $phone){
        $addInTable = new TelegramUser;
        $addInTable->datetime = $datetime;
        $addInTable->name = $name;
        $addInTable->phone = $phone;
        $addInTable->save();
    }
    protected function getEntryFromDb($id): string
    {
       $res = implode( ' ', json_decode(json_encode(TelegramUser::query()->find($id), true),true));
       $array = explode(' ', $res);
       $array[1] = $this->getDate($array[1]);
       return implode( '; ', $array);
    }
    protected function phoneMatch($phone){
        $phone_regex = '/^\+?[78][-\(]?\d{3}\)?-?\d{3}-?\d{2}-?\d{2}$/';
        return preg_match($phone_regex, $phone) ? $phone : false;
    }
    protected function nameMatch($name){
        $name_regex = "/^[a-z ,.'-]+$/";
        return preg_match($name_regex, $name) ? $name : false;
    }
}
