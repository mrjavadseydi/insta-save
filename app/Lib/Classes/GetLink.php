<?php

namespace App\Lib\Classes;

use App\Events\UsersRequest;
use App\Jobs\GetIgtvJob;
use App\Jobs\GetPostJob;
use App\Jobs\GetProfileJob;
use App\Jobs\GetStoryJob;
use App\Lib\Interfaces\TelegramOprator;
use App\Models\Page;
use App\Models\Subscription;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class GetLink  extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type=="message");
    }

    public function handel()
    {
        $this->text = explode('?',$this->text)[0];

        $check = $this->getType($this->text);
        if ($check=="error"){
            return sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str('لینک ارسالی شما صحیح نمیباشد !')->toString()
            ]);
        }
        $subscribe =hasRequest($this->chat_id);
        $cookie = Page::where('member_id',$this->user->id)->first();
        if (!$subscribe&&!$cookie){
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str('شما سکه ندارید! ')
                    ->append("\n")
                    ->append('برای دانلود نیاز است اکانت اینستاگرام خود را اضافه کنید')
                    ->append("\n")
                    ->append('برای اضافه کردن اکانت خود دستور /add را ارسال کنید .')
//                    ->append("\n")
//                    ->append("در صورت عدم تمایل به اضافه کردن حساب خود میتوانید اشتراک ویژه خریداری کنید و بدون اکانت دانلود کنید")
//                    ->append("\n")
//                    ->append("برای خرید اکانت ویژه دستور /vip را ارسال کنید.")
                    ->toString()
            ]);
            return false;
        }
        if(\Cache::has('download_'.$this->chat_id)&&! isAdmin($this->chat_id)){
           $remain =  now()->diffInSeconds(\Cache::get('download_'.$this->chat_id));
//            $carbon = new Carbon(\Cache::get('download_'.$this->chat_id))->diff;
            $text = "⚠️ شما در هر دقیقه تنها یکبار میتوانید درخواست دانلود ارسال کنید.

⏳ لطفا $remain ثانیه دیگر مجدد تلاش کنید.";
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>$text

            ]);
            return 0;
        }
        \Cache::put('download_'.$this->chat_id,now()->addMinute(),60);

        switch ($check){
            case "profile":
                sendMessage([
                    'chat_id'=>$this->chat_id,
                    'text'=>str(config('text.wait'))->toString()
                ]);
                $mg = sendMessage([
                    'chat_id'=>$this->chat_id,
                    'text'=>"⌛وضعیت : در صف انتظار "
                ]);
                $mg = $mg['message_id'];
                event(new UsersRequest($this->chat_id,$check,$this->text));
                $username = str_replace('https://www.instagram.com/','',$this->text);
                $username = str_replace('http://www.instagram.com/','',$username);
                $username = str_replace('https://instagram.com/','',$username);
                $username = str_replace('http://instagram.com/','',$username);
                $username = str_replace('/','',$username);
                GetProfileJob::dispatch($this->chat_id,$username,$mg);
                break;
            case "post":
            case "igtv":
            case "reel":
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str(config('text.wait'))->toString()
            ]);
            $mg = sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>"⌛وضعیت : در صف انتظار "
            ]);
            $mg = $mg['message_id'];
            event(new UsersRequest($this->chat_id,$check,$this->text));
            GetPostJob::dispatch($this->text,$this->chat_id,$mg);
                break;
            case "story":
                sendMessage([
                    'chat_id'=>$this->chat_id,
                    'text'=>str(config('text.wait'))->toString()
                ]);
                $mg = sendMessage([
                    'chat_id'=>$this->chat_id,
                    'text'=>"⌛وضعیت : در صف انتظار "
                ]);
                $mg = $mg['message_id'];
                event(new UsersRequest($this->chat_id,$check,$this->text));
                GetStoryJob::dispatch($this->text,$this->chat_id,$mg);
                break;

        }

    }
    public  function getType($link){
        $link = str_replace('https://instagram.com/','https://www.instagram.com/',$link);
        $validator = \Validator::make(['link'=>$link],[
            'link'=>'starts_with:https://www.instagram.com/',
        ]);
        $ex = array_filter(explode('/',str_replace('https://www.instagram.com/','',$link)));
        if ($validator->fails()||count($ex)<1)
            return 'error';

        if (!isset($ex[1])){
            return 'profile';
        }
        switch ($ex[0]){
            case 'p':
                return 'post';
                break;
            case 'tv':
                return "igtv";
                break;
            case 'stories':
                return 'story';
                break;
            case 'reel':
                return 'reel';
                break;
        }
        return  'error';
    }
}
