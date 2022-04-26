<?php

namespace App\Lib\Classes\Admin;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Page;
use App\Models\Report;
use Telegram\Bot\FileUpload\InputFile;

class SendAll extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && isAdmin($this->chat_id) && ($this->text == "پیام همگانی")&&getState($this->chat_id)=="admin_menu");
    }

    public function handel()
    {

        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>"پیام مورد نظر را ارسال کنید",
            'reply_markup'=>backButton()
        ]);
        setState($this->chat_id,'send_to_all');
    }
}
