<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chat_id,$text;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$text)
    {
        $this->chat_id = $chat_id;
        $this->text = $text;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $this->text
        ]);
    }
}
