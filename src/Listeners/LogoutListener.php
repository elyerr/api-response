<?php

namespace Elyerr\ApiExtend\Listeners;
 
use Elyerr\ApiExtend\Events\LogoutEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogoutListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LogoutEvent $event): void
    {
        //
    }
}
