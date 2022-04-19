<?php

namespace App\Lib\Classes;

use App\Jobs\LoginJob;
use App\Lib\Interfaces\TelegramOprator;

class LoginAccount extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=='message'&&getState($this->chat_id)=="add_account");
    }

    public function handel()
    {
        if(\Cache::has('login_'.$this->chat_id)){
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str(" ")->append("شما در هر دقیقه تنها یکبار میتوانید برای افزودن اکانت تلاش کنید")->toString()

            ]);
            return 0;
        }
        \Cache::put('login_'.$this->chat_id,'1',60);
        $ex = explode("\n",$this->text);
        if (count($ex)!=2){
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str(" ")->append("فرمت ارسالی صحیح نمیباشد ، لطفا در خط اول نام کاربری و در خط دوم پسورد اکانت خود راارسال کنید")->toString()

            ]);
            return 0;
        }
        LoginJob::dispatch($this->chat_id,$ex[0],$ex[1]);
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str()->append('در حال ورود ، لطفا صبر کنید...')
            ->toString()
        ]);
    }

}
