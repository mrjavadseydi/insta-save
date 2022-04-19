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
    public $chat_id,$username;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$username)
    {
        $this->chat_id = $chat_id;
        $this->username = $username;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cookie = getCookie($this->chat_id);
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/user/id_from_username',[
            'username' => $this->username,
            'cookie' => $cookie
        ]);
        $id = $request->body();
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/user/info',[
            'id' => $id,
            'cookie' => $cookie
        ]);
        $response = json_decode($request->body(),true);
        if (isset($response['profile_pic_url_hd'])) {
            $request = Http::timeout(130)->get($response['profile_pic_url_hd']);
            $file = $request->body();
            $file_temp_name = uniqid();
            Storage::disk('public')->put($file_temp_name,$file);
            SendMediaToUser::dispatch($this->chat_id,$file_temp_name);
        }
        if (isset($response['biography'])){
            sendMessage([
                'chat_id' => $this->chat_id,
                'text' => $response['biography']."\n دانلود شده از ربات اینستا سیو"
            ]);
        }

    }
}
