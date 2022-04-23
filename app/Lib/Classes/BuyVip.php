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

        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str("")->append('اشتراک مورد نظر خود را انتخاب کنید')
            ->toString(),
            'reply_markup'=>plans_key()
        ]);
    }

}
