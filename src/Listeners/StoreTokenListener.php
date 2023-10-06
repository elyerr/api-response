<?php

namespace Elyerr\ApiExtend\Listeners;
 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Elyerr\ApiExtend\Events\StoreTokenEvent;

class StoreTokenListener
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
    public function handle(StoreTokenEvent $event): void
    {
        //
    }
}
