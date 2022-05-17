<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GetProfileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chat_id,$username,$mg;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$username,$mg)
    {
        $this->chat_id = $chat_id;
        $this->username = $username;
        $this->mg = $mg;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cookie = getCookie($this->chat_id);
        editMessageText([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg,
            'text' => '⌛وضعیت : دریافت پروفایل ',
        ]);
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/user/id_from_username',[
            'username' => $this->username,
            'sessionid' => $cookie
        ]);
        $res =  json_decode($request->body(),true);
        if (!DeadCookie($res,$cookie,$this->chat_id)) {
            return;
        }
        $id = str_replace('"','',$request->body());
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/user/info',[
            'user_id' => $id,
            'sessionid' => $cookie
        ]);
        $response = json_decode($request->body(),true);
        editMessageText([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg,
            'text' => '⌛وضعیت : دانلود پروفایل از اینستاگرام',
        ]);
        if (isset($response['profile_pic_url_hd'])||isset($response['profile_pic_url'])) {
            $url = $response['profile_pic_url_hd'] ?? $response['profile_pic_url'];
            $request = Http::timeout(130)->get($url);
            $file_temp_name = uniqid();
            file_put_contents(public_path($file_temp_name),$request->body());
            $txt = str("نام صاحب حساب: ".$response['full_name'])->append("\n\n")->append("بیو:")->append($response['biography']??" ")->append("\n\n")
            ->append("نوع حساب :")
                ->append($response['is_private']?"خصوصی":"عمومی")
                ->append("\n\n")
                ->append("تعداد فالور ها : ")
                ->append($response['follower_count'])
                ->append("\n\n")
                ->append("تعداد فالوینگ ها : ")
                ->append($response['following_count'])
                ->toString()
            ;
            editMessageText([
                'chat_id' => $this->chat_id,
                'message_id' => $this->mg,
                'text' => '⌛وضعیت : ارسال فایل به تلگرام',
            ]);
            SendMediaToUser::dispatch($this->chat_id,$file_temp_name,$txt,$this->mg);
            (hasRequest($this->chat_id)&&subRequestCount($this->chat_id));
        }else{
            sendMessage([
                'chat_id' => $this->chat_id,
                'text' => "کاربر پروفایل نداشت یا پروفایل شخص قابل دانلود نبود"
            ]);
        }

    }
}
