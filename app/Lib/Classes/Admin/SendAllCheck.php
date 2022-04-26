<?php

namespace App\Lib\Classes\Admin;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Page;
use App\Models\Report;
use Telegram\Bot\FileUpload\InputFile;

class SendAllCheck extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && isAdmin($this->chat_id)&&getState($this->chat_id)=="send_to_all");
    }

    public function handel()
    {
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>$this->text,
        ]);
        \Cache::put('send_to_all',$this->text);
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>"پیام بالا برای همه کاربران ارسال خواهد شد ! ایا مطمئن هستید؟",
            'reply_markup'=>sendAllMenu()
        ]);

        setState($this->chat_id,'send_to_all_check');
    }
}
