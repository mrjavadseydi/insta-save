<?php

namespace App\Lib\Classes\Admin;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use Telegram\Bot\FileUpload\InputFile;

class Menu extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && isAdmin($this->chat_id) && ($this->text == "/panel"||$this->text == "مدیریت"||$this->text=="انصراف"));
    }

    public function handel()
    {
        if (\Cache::has('send_to_all')){
            \Cache::forget('send_to_all');
        }

        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>"welcome admin",
            'reply_markup'=>adminMenu()
        ]);
        setState($this->chat_id,'admin_menu');
    }
}
