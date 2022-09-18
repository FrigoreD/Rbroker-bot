<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram;
use App\Models\TelegramUser;
use Carbon\Carbon;
use Illuminate\Http\Request;


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
    public function index(Request $request, Telegram $telegram): void{
        $message = $telegram->text($request);
        /**
         * The logic is based on switch/case construction, depending on user's message
         */
        switch ($message){
            case '/help':
                $text = "Список полезных функций:\n/add datetime; name; phone - Добавление новой строки\n/del id - удаление строки\n/sel id - Вывести содержимое строки";
                break;
            /**
             * case /add
             */
            case (bool)preg_match('/\/add\s(.*);\s(.*);\s(.*)/', $message, $res):
                try{
                    /**
                     * RGXs for incoming date
                     */
                    if(!$name = $this->nameMatch($res[2])){
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
                    /**
                     * creating new row in DB
                     */
                    $this->newLine($date, $name, $phone);
                    $text = 'Добавлена запись '. $this->getLastId() . ': ' . $res[1] . '; ' . $name . '; ' . $phone;
                    /**
                     * we often use try/catch constructions to catch some exceptions
                     */
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
                    /**
                     * We can delete row like this thanks to model
                     */
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
                    /**
                     * method that can output date from db
                     */
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
     * print date in that way: d.m.y h:m from UNIX TIMESTAMP
     * @param int $date
     * @return string
     */
    public function getDate(int $date): string{
        return Carbon::createFromTimestamp($date)->format('d.m.y h:m');
    }

    /**
     * add new data to DB
     * @param string $datetime
     * @param string $name
     * @param string $phone
     * @return void
     */
    public function newLine(string $datetime, string $name, string $phone): void{
        $addInTable = new TelegramUser;
        $addInTable->datetime = $datetime;
        $addInTable->name = $name;
        $addInTable->phone = $phone;
        $addInTable->save();
    }

    /**
     * Get date from DB
     * @param int $id
     * @return string
     */
    public function getEntryFromDb(int $id): string
    {
        $res = implode( ' ', json_decode(json_encode(TelegramUser::query()->find($id), true),true));
        $array = explode(' ', $res);
        $array[1] = $this->getDate($array[1]);
        return implode( '; ', $array);
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function phoneMatch(string $phone){
        $phone_regex = '/^\+?[78][-\(]?\d{3}\)?-?\d{3}-?\d{2}-?\d{2}$/';
        return preg_match($phone_regex, $phone) ? $phone : false;
    }

    /**
     * @param string $name
     * @return false|string
     */
    public function nameMatch(string $name)
    {
        $name_regex = "/^(([A-Za-zА-Яа-я]+[,.]?[ ]?|[a-z]+['-]?)+)$/";
        return preg_match($name_regex, $name) ? $name : false;
    }

    /**
     * @return int
     */
    public function getLastId(): int {
        $data =  TelegramUser::query()->latest('id')->first();
        return $id = $data['id'];
    }

    /**
     * @param $date
     * @return float|int|string|void
     */
    public function setDate($date){
        $formats = ['Y-m-d','d-m-Y', 'Y/m/d', 'd/m/Y', 'Y m d', 'd m Y', 'd.m.Y', 'Y.m.d'];
        foreach ($formats as $format) {
            try{
                return Carbon::createFromFormat($format, $date) -> timestamp;
            } catch (\Throwable $e){}
        }
    }
}

