<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;

class Help extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="🤔 راهنما"));
    }

    public function handel()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.help'),
//            'reply_markup'=>mainMenu()
        ]);
        setState($this->chat_id);
    }
}
