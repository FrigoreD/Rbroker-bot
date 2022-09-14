<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TelegramUser;


/**
 * Our rbroker_bot Class
 */
class TelegramUserController extends Controller
{
    /**
     * Our rbroker_bot that processes the user's message and gives a response based on this
     * @param Request $request
     * @param Telegram $telegram
     * @return void
     */
    public function index(Request $request, Telegram $telegram){
        $message = $telegram->text($request);
        //The logic is based on switch/case construction, depending on user's message
        switch ($message){
            case '/help':
                $text = "Список полезных функций:\n/add datetime; name; phone - Добавление новой строки\n/del id - удаление строки\n/sel id - Вывести содержимое строки";
                break;
            /**
             * case /add
             */
            case (bool)preg_match('/\/add\s(.*);\s(.*);\s(.*)/', $message, $res):
                try{
                    //RGXs for incoming date
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
                    //creating new row in DB
                    $this->newLine($date, $phone, $phone);
                    $text = 'Добавлена запись: '. $res[1] . ' ' . $phone . ' ' . $phone;
                    //we often use try/catch constructions to catch some exceptions
                }catch (\Throwable $e){
                        $text = 'Пользователь с таким номером телефона уже существует или неверно введена дата';
                }
                break;
            /**
             * case /del
             */
            case (bool)preg_match('/\/del\s(.*)/', $message, $res):
                $id = $res[1];
                try {
                    $data = $this->getEntryFromDb($id);
                    //We can delete row like this thanks to model
                    TelegramUser::query()->find($id)->delete();
                    $text = 'Строка ' . $data . ' Была удалена';
                }catch (\Throwable $e){
                    $text = 'Строки с id: ' . $id . ' не существует';
                }
                break;
            /**
             * case /sel
             */
            case (bool)preg_match('/\/sel\s(.*)/', $message, $res):
                $id = $res[1];
                try{
                    //method that can output date from db
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

    /**
     * simple regex that helps user to print date like Y-m-d or d m y and e.t.c to UNIX TIMESTAMP
     * @param $date
     * @return float|int|string|void
     */
    protected function setDate($date){
        $formats = ['Y-m-d','d-m-Y', 'Y/m/d', 'd/m/Y', 'Y m d', 'd m Y', 'd.m.Y', 'Y.m.d'];
        foreach ($formats as $format) {
            try{
               return Carbon::createFromFormat($format, $date) -> timestamp;
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * print date in that way: d.m.y h:m from UNIX TIMESTAMP
     * @param $date
     * @return string
     */
    protected function getDate($date){
        return Carbon::createFromTimestamp($date)->format('d.m.y h:m');
    }

    /**
     * add new data to DB
     * @param $datetime
     * @param $name
     * @param $phone
     * @return void
     */
    protected function newLine($datetime, $name, $phone){
        $addInTable = new TelegramUser;
        $addInTable->datetime = $datetime;
        $addInTable->name = $name;
        $addInTable->phone = $phone;
        $addInTable->save();
    }

    /**
     * Get date from DB
     * @param $id
     * @return string
     */
    protected function getEntryFromDb($id): string
    {
       $res = implode( ' ', json_decode(json_encode(TelegramUser::query()->find($id), true),true));
       $array = explode(' ', $res);
       $array[1] = $this->getDate($array[1]);
       return implode( '; ', $array);
    }

    /**
     * Regex phone
     * @param $phone
     * @return false
     */
    protected function phoneMatch($phone){
        $phone_regex = '/^\+?[78][-\(]?\d{3}\)?-?\d{3}-?\d{2}-?\d{2}$/';
        return preg_match($phone_regex, $phone) ? $phone : false;
    }

    /**
     * Regex name
     * @param $name
     * @return false
     */
    protected function nameMatch($name){
        $name_regex = "/^[a-z ,.'-]+$/";
        return preg_match($name_regex, $name) ? $name : false;
    }
}
