<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use Carbon\Carbon;

class MediaBlocker extends TelegramOprator
{

    public function initCheck()
    {
        if ($this->message_type == "message") {
            if (str_starts_with($this->text, "/start inv_")) {
                $from = str_replace('/start inv_', '', $this->text);
                $from = Member::where('id', base64_decode(safeBase64Return($from)))->first();
                $is_invited = Invite::where('chat_id', $this->chat_id)->first();
                $user_created = new Carbon($this->user->created_at);
                $now = Carbon::now();
                $diff = $now->diffInSeconds($user_created);
                if (!$is_invited && $diff < 10 && $from) {
                    $from->update([
                        'request_count' => $from->request_count + config('bot.invite')
                    ]);
                    Invite::create([
                        'chat_id' => $this->chat_id,
                        'member_id' => $from->id
                    ]);
                    $text = str_replace('%coin%',config('bot.invite'),config('text.invite_accepted'));
                    $text = str_replace('%wallet%',$from->request_count,$text);
                    sendMessage([
                        'chat_id' => $from->chat_id,
                        'text' => $text,
                    ]);
                }
            }
        }
        return ($this->message_type != "message"&&$this->message_type != "callback_query");

    }

    public function handel()
    {
    }
    public function reverseRandomStr($text){
        $arr = [
            'G'=>1,'I'=>2,'N'=>3,'O'=>4,'P'=>5,'A'=>6,
            'M'=>7,'B'=>8,'C'=>9,'D'=>10
            ,'H'=>11,'E'=>12,'F'=>13,'J'=>14,'K'=>15,'L'=>16,'Q'=>20,'R'=>21,'S'=>22,
            'T'=>23,'U'=>24,'V'=>25,'W'=>26,'X'=>27,'Y'=>28,'Z'=>29
        ];
        $str = '';
        $text = str_split($text);
        foreach ($text as $t){
            $str .= $arr[$t-1];
        }
        return $str;
    }
}
