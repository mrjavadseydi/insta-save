<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;

class JoinCheck extends TelegramOprator
{

    public function initCheck()
    {
        if (\Cache::has('InstaPich_'.$this->chat_id)){
            return  false;
        }
        if (joinCheck($this->chat_id,'InstaPich')){
            \Cache::put('InstaPich_'.$this->chat_id, 'InstaPich', 180);
            return false;
        }
        else{
            return true;
        }

    }

    public function handel()
    {
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>config('text.join_channel'),
        ]);
    }

}
