<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\FileUpload\InputFile;

class SendMediaToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $chat_id;
    public $media;
    public $caption;
    /**
     * @var false
     */
    private  $mg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chat_id,$media,$caption="",$mg=false)
    {
        $this->chat_id = $chat_id;
        $this->media = $media;
        $this->caption = $caption." \n". config('text.caption');

        $this->mg = $mg;
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
            sendMessage([
                'chat_id' => $this->chat_id,
                'text'=>'فرمت فایل شناسایی نشد'
            ]);
            return unlink(public_path($this->media));
        }
        $ex  = explode('/',$type);
        $file = InputFile::create(
            asset($this->media),
            $this->media.'.'.$ex[1]
        );
        if (strlen($this->caption)>1500){
            SendMessageJob::dispatch($this->chat_id,$this->caption)->delay(5);
            $this->caption =  config('text.caption');
        }
        if ($ex[0] == 'image') {
                sendPhoto([
                    'chat_id' => $this->chat_id,
                    'photo' => $file,
                    'caption' => $this->caption
                ]);

        }elseif ($ex[0] == 'video'){
                sendVideo([
                    'chat_id' => $this->chat_id,
                    'video' => $file,
                    'caption' => $this->caption
                ]);
        }else{
            sendMessage([
                'chat_id' => $this->chat_id,
                'text'=>'فرمت فایل شناسایی نشد'
            ]);
        }
        return unlink(public_path($this->media));

    }
    public function __destruct()
    {
        deleteMessage([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg
        ]);
    }
}
