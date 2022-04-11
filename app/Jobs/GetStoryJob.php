<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetStoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $url ,$chat_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url,$chat_id)
    {
        $this->url = $url;
        $this->chat_id = $chat_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscribe = Subscription::where('chat_id',$this->chat_id)->whereBetween(now(),
            ['start','end']
        )->first();
        if ($subscribe){
            $coockie = Page::orderBy('id','asc')->first()->coockie;
        }else{
            $coockie = Page::where('chat_id',$this->chat_id)->first()->coockie;
        }
        $url = $this->url;
        $ex =array_filter(explode('/',$url));
        $id = end($ex);
        //16705606368%3AdZAgjw7y9zBM3S%3A1311
        $request = Http::asForm()->post("http://194.5.192.39:8000/story/download/",[
            'sessionid' => $coockie,
            'story_pk' => $id,
            'return_file'=>true,
        ]);
        //mime_content_type
    }
}
