<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;

class JoinCheck extends TelegramOprator
{

    public function initCheck()
    {
        return (getConfig('join')&&!joinCheck($this->chat_id,getConfig('channel')));
    }

    public function handel()
    {
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>str()->append('برای استفاده از ربات باید در کانال ما عضو شوید!')->append("\n")
            ->append('@'.getConfig('channel'))->toString()
        ]);
    }

}
