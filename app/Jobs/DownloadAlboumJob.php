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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($info,$chat_id,$cookie)
    {
        $this->info=$info;
        $this->chat_id=$chat_id;
        $this->cookie = $cookie;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->info['resources'] as $resource){
            if ($resource['media_type']==2){
                DownloadVideoJob::dispatch($this->chat_id,$resource['video_url'],$this->cookie,false,[]);
            }elseif ($resource['media_type']==1){
                DownloadPhotoJob::dispatch($this->chat_id,$resource['thumbnail_url'],$this->cookie,false,[]);
            }
        }
        SendMessageJob::dispatch($this->chat_id,str($this->info['caption_text']??"")->append("\n")
            ->append(config('text.caption'))->toString());

    }
}
