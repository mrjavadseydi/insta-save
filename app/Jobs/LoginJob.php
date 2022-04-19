<?php

namespace App\Jobs;

use App\Models\Member;
use App\Models\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class LoginJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chat_id, $username, $password;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id, $username, $password)
    {
        $this->chat_id = $chat_id;
        $this->username = $username;
        $this->password = $password;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/auth/login', [
            'username' => $this->username,
            'password' => $this->password,
        ]);
        $body = $request->body();
        if (strpos($body, '}') !== false) {
            $json = json_decode($body, true);
            $text = 'خطایی رخ داده است لطفا مجددا تلاش کنید';
            if (isset($json['exc_type'])) {
                if ($json['exc_type'] == 'BadPassword') {
                    $text = 'نام کاربری یا رمز عبور اشتباه است';
                }
                sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => $text,
                ]);

            }
        } else {
            sendMessage([
                'chat_id' => $this->chat_id,
                'text' => 'ورود موفقیت آمیز بود ! ',
            ]);
            Page::create([
                'member_id' => Member::where('chat_id', $this->chat_id)->first()->id,
                'username' => $this->username,
                'password' => $this->password,
                'coockie' => str_replace('"','',$body),
            ]);
            setState($this->chat_id);
        }
    }

}
