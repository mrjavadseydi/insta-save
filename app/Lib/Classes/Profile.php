<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Page;

class Profile extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&($this->text=="👤 حساب کاربری من"));
    }

    public function handel()
    {
        $text = str('اطلاعات کاربری شما')->append("\n")
        ->append('میزان سکه : ')->append($this->user->request_count)->append("\n")
            ->append('تعداد حساب کاربری های اضافه شده : ')->append(Page::where('member_id',$this->user->id)->count())->append("\n")
            ->append('تعداد دعوت های موفق شما :')->append(Invite::where('member_id',$this->user->id)->count())->toString();
        ;
        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>$text,
//            'reply_markup'=>mainMenu()
        ]);
        setState($this->chat_id);
    }
}
