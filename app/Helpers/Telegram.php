<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


/**
 * Our brains of all kinds of bots
 * here some methods, that we use to send message,
 * parse incoming requests and receive our text
 */
class Telegram{
    protected $http;
    protected $TOKEN;
    const url = 'https://api.telegram.org/bot';
    public function __construct(Http $http, $TOKEN){
        $this->http = $http;
        $this->TOKEN = $TOKEN;
    }

    /**
     * send message to user by chat id
     * @param $request
     * @param $text
     * @return void
     */
    public function sendMessage($request, $text){
        $this->http::post(self::url.$this->TOKEN."/sendMessage", [
            'chat_id' => $this->chatId($request),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
}

    /**
     * parse chat id from request
     * @param Request $request
     * @return mixed
     */
    protected function chatId(Request $request){
        try{
            $chat = $request->input('message')['chat'];
        }catch (\Throwable $e){
            $chat = $request->input('channel_post')['chat'];
        }
        return $id = $chat['id'];
    }

    /**
     * parse text from request
     * @param Request $request
     * @return mixed
     */
    public function text(Request $request) {
        try{
            return $request->input('message')['text'];
        }catch (\Throwable $e){
            return $request->input('channel_post')['text'];
        }
    }

}
