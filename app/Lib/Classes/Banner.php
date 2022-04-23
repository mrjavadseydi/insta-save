<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;

class Banner extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="🚸 معرفی به دوستان (سکه رایگان)"));
    }

    public function handel()
    {
        $url = "https://t.me/InstaPichBot?start=inv_".$this->chat_id;
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>$url,
//            'reply_markup'=>mainMenu()
        ]);
        setState($this->chat_id);
    }
}
