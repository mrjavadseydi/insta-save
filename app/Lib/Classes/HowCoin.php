<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;
use Telegram\Bot\FileUpload\InputFile;

class HowCoin extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="/help_credit"));
    }

    public function handel()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.how_coin'),
        ]);
        setState($this->chat_id);
    }
}
