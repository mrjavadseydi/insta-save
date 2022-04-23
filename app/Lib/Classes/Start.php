<?php
namespace App\Lib\Classes;
use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use Carbon\Carbon;

class Start extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message"&&(str_starts_with($this->text, "/start") ||$this->text=="بازگشت ↪️"));
    }

    public function handel()
    {

        sendMessage([
            'chat_id' => $this->chat_id,
            'text'=>config('text.start'),
            'reply_markup'=>mainMenu()
        ]);
        setState($this->chat_id);

    }
}
