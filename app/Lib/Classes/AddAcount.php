<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;

class AddAcount extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=='message'&&$this->text=="/add");
    }

    public function handel()
    {
        setState($this->chat_id,'add_account');
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str()->append('برای افزودن اکانت در خط اول نام کاربری و در خط دوم کلمه عبور خود را وارد کنید ')
            ->append("\n")
            ->append("مثال:")
            ->append("instauser")
            ->append("\n")
            ->append("password")
            ->toString()
        ]);
    }

}
