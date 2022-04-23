<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;

class BuyVipCallBack extends TelegramOprator
{
    public function initCheck()
    {
        if ($this->message_type=="callback_query"){
            $ex = explode('-',$this->data);
            if ($ex[0]=="vip"){
                return true;
            }
        }
        return false;
    }

    public function handel()
    {
        $ex = explode('-',$this->data);
        deleteMessage([
            'chat_id'=>$this->chat_id,
            'message_id'=>$this->message_id
        ]);
        $plan = Plan::whereId($ex[1])->first();
        $payment = Payment::create([
            'member_id'=>$this->user->id,
            'amount'=>$plan->price,
            'plan_id'=>$plan->id,
            'order'=>time().rand(0,1000)
        ]);
        $url = route('pay.redirect',$payment->id);
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str('برای پرداخت بر روی لینک زیر کلیک کنید ')
                ->append("\n")
                ->append($url)
                ->append("\n")
                ->toString()
        ]);
    }

}
