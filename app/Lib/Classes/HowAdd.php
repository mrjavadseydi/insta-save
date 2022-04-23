<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;

class HowAdd extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="/help_add"));
    }

    public function handel()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.how_add'),
//            'reply_markup'=>mainMenu()
        ]);
        setState($this->chat_id);
    }
}
