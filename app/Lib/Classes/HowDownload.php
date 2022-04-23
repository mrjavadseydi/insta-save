<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;

class HowDownload extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="/help_download"));
    }

    public function handel()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.how_download'),
        ]);
        setState($this->chat_id);
    }
}
