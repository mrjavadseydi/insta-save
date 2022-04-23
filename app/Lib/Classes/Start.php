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

        if (str_starts_with($this->text, "/start inv_")){
            $from = str_replace('/start inv_', '', $this->text);
            $from = Member::where('chat_id', $from)->first();
            $is_invited = Invite::where('chat_id', $this->chat_id)->first();
            $user_created = new Carbon($this->user->created_at);
            $now = Carbon::now();
            $diff = $now->diffInSeconds($user_created);
            if(!$is_invited&&$diff<10&&$from){
                $from->update([
                    'request_count'=>$from->request_count+config('bot.invite')
                ]);
                Invite::create([
                    'chat_id'=>$this->chat_id,
                    'member_id'=>$from->id
                ]);
                sendMessage([
                    'chat_id' => $from->chat_id,
                    'text'=>config('text.invite_accepted'),
                ]);
            }
        }
    }
}
