<?php

namespace App\Listeners;

use App\Events\UsersRequest;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UsersRequestListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UsersRequest $event)
    {
        Report::query()->create([
            'chat_id'=>$event->chat_id,
            'action'=>$event->action,
            'meta'=>$event->link
        ]);
    }
}
