<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;

class HowInvite extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="/help_link"));
    }

    public function handel()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.how_invite'),
        ]);
        setState($this->chat_id);
    }
}
