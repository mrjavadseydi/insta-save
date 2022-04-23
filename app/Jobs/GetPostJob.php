<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $link;
    public $chat_id;
    public $cookie;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($link,$chat_id)
    {
        $this->link = $link;
        $this->chat_id = $chat_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->cookie = getCookie($this->chat_id);
        $info = $this->getInfo();
        if ($info['media_type']==8){
            DownloadAlboumJob::dispatch($info,$this->chat_id,$this->cookie);
            (hasRequest($this->chat_id)&&subRequestCount($this->chat_id));


        }elseif ($info['media_type']==2){
            DownloadVideoJob::dispatch($this->chat_id,$info['video_url'],$this->cookie,true,$info);
            (hasRequest($this->chat_id)&&subRequestCount($this->chat_id));


        }elseif ($info['media_type']==1){
            DownloadPhotoJob::dispatch($this->chat_id,$info['thumbnail_url'],$this->cookie,true,$info);
            (hasRequest($this->chat_id)&&subRequestCount($this->chat_id));

        }
    }
    public function getInfo(){

        $request = Http::timeout(130)->asForm()->get('http://194.5.192.39:8000/media/pk_from_url', [
            'url'=>$this->link,
        ]);
        $pk = str_replace('"','',$request->body());
        $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/media/info',[
            'sessionid'=>$this->cookie,
            'pk'=>(int)$pk,
        ]);
        return json_decode($request->body(),true);

    }

}
