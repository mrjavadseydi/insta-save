<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;
use Telegram\Bot\FileUpload\InputFile;

class HowCopy extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="/help_copy_link"));
    }

    public function handel()
    {
        $file = InputFile::create(public_path('copy.jpg'),'copy.jpg');
        sendPhoto([
            'chat_id' => $this->chat_id,
            'caption'=>config('text.how_copy'),
            'photo'=>$file
        ]);
        setState($this->chat_id);
    }
}
