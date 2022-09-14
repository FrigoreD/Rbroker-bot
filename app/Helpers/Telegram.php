<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Telegram{
    protected $http;
    protected $TOKEN;
    const url = 'https://api.telegram.org/bot';
    public function __construct(Http $http, $TOKEN){
        $this->http = $http;
        $this->TOKEN = $TOKEN;
    }
    public function sendMessage($request, $text){
        $this->http::post(self::url.$this->TOKEN."/sendMessage", [
            'chat_id' => $this->chatId($request),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
}
    protected function chatId(Request $request){
        try{
            $chat = $request->input('message')['chat'];
        }catch (\Throwable $e){
            $chat = $request->input('channel_post')['chat'];
        }
        return $id = $chat['id'];
    }

    public function text(Request $request) {
        try{
            return $request->input('message')['text'];
        }catch (\Throwable $e){
            return $request->input('channel_post')['text'];
        }
    }

//    public function testSendMessage($chatId, $text){
//        $this->http::post(self::url.$this->TOKEN."/sendMessage", [
//            'chat_id' => $chatId,
//            'text' => $text,
//            'parse_mode' => 'html',
//        ]);
//    }
}
