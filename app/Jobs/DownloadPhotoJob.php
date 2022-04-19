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

class DownloadPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chat_id,$url,$cookie,$resource,$send_caption;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$url,$cookie,$send_caption=true,$resource)
    {
        $this->chat_id = $chat_id;
        $this->url = $url;
        $this->cookie = $cookie;
        $this->send_caption = $send_caption;
        $this->resource = $resource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = Http::timeout(130)->asForm()->get($this->url);
        $file_temp_name = uniqid();
        file_put_contents(public_path($file_temp_name),$request->body());

        SendMediaToUser::dispatch($this->chat_id,$file_temp_name);
        if ($this->send_caption) {
            sendMessage([
                'chat_id'=>$this->chat_id,
                'text'=>str($this->resource['caption_text'])->append("\n")
                    ->append("دانلود شده از طریق ربات اینستا سیو")
            ]);
        }

    }
}
