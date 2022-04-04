<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Subscription;

class BuyVip extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=='message'&&$this->text=="/vip");
    }

    public function handel()
    {
        $subscribe = Subscription::where('chat_id',$this->chat_id)->whereBetween(now(),
            ['start','end']
        )->first();
        if ($subscribe){
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str()->append('شما حساب ویژه فعال دارید ')
                    ->append("\n")
                    ->append("تاریخ انقضا :")
                    ->append($subscribe->end)
                    ->toString()
            ]);
            return 0;
        }

        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str()->append('اشتراک مورد نظر خود را انتخاب کنید')
            ->toString(),
            'reply_markup'=>plans_key()
        ]);
    }

}
