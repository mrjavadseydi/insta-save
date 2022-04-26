<?php

namespace App\Lib\Classes\Admin;

use App\Jobs\SendMessageJob;
use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Page;
use App\Models\Report;
use App\Models\User;
use Telegram\Bot\FileUpload\InputFile;

class SendAllAction extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && isAdmin($this->chat_id) && ($this->text == "ارسال")&&getState($this->chat_id)=="send_to_all_check");
    }

    public function handel()
    {

        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>"در صف ارسال قرار گرفت",
            'reply_markup'=>adminMenu()
        ]);
        $text = \Cache::pull('send_to_all');
        foreach (Member::all() as $user){
            SendMessageJob::dispatch($user->chat_id,$text);
        }
        setState($this->chat_id,'admin_menu');

    }
}
