<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\FileUpload\InputFile;

class SendMediaToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chat_id;
    public $media;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$media)
    {
        $this->chat_id = $chat_id;
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = mime_content_type(public_path($this->media));
        if (!$type){
            return sendMessage([
                'chat_id' => $this->chat_id,
                'text'=>'فرمت فایل شناسایی نشد'
            ]);
        }
        $ex  = explode('/',$type);
        $file = InputFile::create(
            asset($this->media),
            $this->media.'.'.$ex[1]
        );
        if ($ex[0] == 'image') {

            \Storage::disk('public')->move($this->media, $this->media.'.'.$ex[1]);
            return sendPhoto([
                'chat_id' => $this->chat_id,
                'photo' => $file
            ]);
        }elseif ($ex[0] == 'video'){
            \Storage::disk('public')->move($this->media, $this->media.'.'.$ex[1]);
            return sendVideo([
                'chat_id' => $this->chat_id,
                'video' => $file
            ]);
        }

    }
}
