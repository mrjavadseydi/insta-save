<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use Telegram\Bot\FileUpload\InputFile;

class Banner extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && ($this->text == "🚸 معرفی به دوستان (سکه رایگان)"));
    }

    public function handel()
    {
        $url = "https://t.me/InstaPichBot?start=inv_" . $this->chat_id;
        $file = InputFile::create(
            public_path('banner.jpg'),
            'banner.jpg'
        );
        $text = str_replace("%link%",$url,config('text.banner'));
        $res = sendPhoto([
            'chat_id' => $this->chat_id,
            'caption' => $text,
            'photo' => $file
//            'reply_markup'=>mainMenu()
        ]);
        $repl = config('text.reply_banner');
        $invite = Invite::where('member_id',$this->user->id)->count();
        $coin = config('bot.invite');
        $repl = str_replace('%coin%',$coin,$repl);
        $repl = str_replace('%invite%',$invite,$repl);
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>$repl,
            'reply_to_message_id'=>$res['message_id']
        ]);
    }
}
