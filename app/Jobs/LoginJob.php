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
            $text = config('text.faild_login');
            if (isset($json['exc_type'])) {
                if ($json['exc_type'] == 'BadPassword') {
                    $text = '❌ نام کاربری یا رمز عبور اشتباه است.

لطفا نام کاربری و رمز عبور را بررسی کرده و مجدد ارسال کنید.';
                    sendMessage([
                        'chat_id' => $this->chat_id,
                        'text' => $text,
                    ]);
                } elseif ($json['exc_type'] == "EOFError") {
                    $text = '❌ورود با خطا مواجه شد در حال تلاش مجدد!';
                    sendMessage([
                        'chat_id' => $this->chat_id,
                        'text' => $text,
                    ]);
                    LoginJob::dispatch($this->chat_id, $this->username, $this->password)->delay(now()->addSeconds(10));
                    return;
                } elseif ($json['exc_type'] == "UnknownError" && $json['detail'] == "The username you entered doesn't appear to belong to an account. Please check your username and try again.") {
                    $text = '❌ نام کاربری یا رمز عبور اشتباه است.

لطفا نام کاربری  را بررسی کرده و مجدد ارسال کنید.';

                    sendMessage([
                        'chat_id' => $this->chat_id,
                        'text' => $text,
                    ]);

                } elseif ($json['exc_type'] == "UnknownError" && $json['detail'] == "consent_required") {
                    $text = '❌خطای اینستاگرام

حساب شما تکمیل نشده و امکان ورود را ندارد ،‌لطفا حساب خود را تکمیل کنید و مجددا تلاش کنید';
                    sendMessage([
                        'chat_id' => $this->chat_id,
                        'text' => $text,
                    ]);

                }
            }
        } else {
            sendMessage([
                'chat_id' => $this->chat_id,
                'text' => config('text.success_login'),
                'reply_markup'=>mainMenu()
            ]);
            Page::create([
                'member_id' => Member::where('chat_id', $this->chat_id)->first()->id,
                'username' => $this->username,
                'password' => $this->password,
                'coockie' => str_replace('"', '', $body),
            ]);
            setState($this->chat_id);
        }
    }

}
