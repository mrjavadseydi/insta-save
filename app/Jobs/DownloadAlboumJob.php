<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadAlboumJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $info,$chat_id,$cookie;
    /**
     * @var false
     */
    private  $mg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($info,$chat_id,$cookie,$mg=false)
    {
        $this->info=$info;
        $this->chat_id=$chat_id;
        $this->cookie = $cookie;
        $this->mg = $mg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $i = 1;
        editMessageText([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg,
            'text' => '⌛وضعیت : دانلود آلبوم ',
        ]);
        foreach ($this->info['resources'] as $resource){
            $text = "اسلاید شماره $i \n";
            $i++;
            if ($resource['media_type']==2){
                DownloadVideoJob::dispatch($this->chat_id,$resource['video_url'],$this->cookie,false,[],$text);
            }elseif ($resource['media_type']==1){
                DownloadPhotoJob::dispatch($this->chat_id,$resource['thumbnail_url'],$this->cookie,false,[],$text);
            }
        }
        SendMessageJob::dispatch($this->chat_id,str($this->info['caption_text']??"")->append("\n")
            ->append(config('text.caption'))->toString())->delay(now()->addSeconds(20));

    }
    public function __destruct()
    {
        deleteMessage([
            'chat_id' => $this->chat_id,
            'message_id' => $this->mg
        ]);
    }
}
