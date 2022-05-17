<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GetStoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url, $chat_id;
    private $mg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $chat_id,$mg)
    {
        $this->url = $url;
        $this->chat_id = $chat_id;
        $this->mg = $mg;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $coockie = getCookie($this->chat_id);
        $url = $this->url;
        $ex = array_filter(explode('/', $url));
        $id = explode("?",end($ex))[0];
        editMessageText([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg,
            'text' => '⌛وضعیت : ارسال درخواست دانلود استوری',
        ]);
        $request = Http::timeout(130)->asForm()->post("http://194.5.192.39:8000/story/download/", [
            'sessionid' => $coockie,
            'story_pk' => $id,
            'return_file' => true,
        ]);
        if (isJson($request->body())) {
            $res =  json_decode($request->body(),true);
            if (!DeadCookie($res,$coockie,$this->chat_id)) {
               return;
            }
            return sendMessage([
                'chat_id' => $this->chat_id,
                'text' => 'دانلود نا موفق بود لطفا بعد از ۲ دقیقه مجددا تلاش کنید',
            ]);
        }
        editMessageText([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg,
            'text' => '⌛وضعیت :آپلود استوری به تلگرام',
        ]);
        $file_temp_name = uniqid();
        file_put_contents(public_path($file_temp_name),$request->body());
        SendMediaToUser::dispatch($this->chat_id,$file_temp_name,"",$this->mg);
        (hasRequest($this->chat_id)&&subRequestCount($this->chat_id));

    }


}
