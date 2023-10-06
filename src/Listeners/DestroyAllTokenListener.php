<?php

namespace Elyerr\ApiExtend\Listeners;
 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Elyerr\ApiExtend\Events\DestroyAllTokenEvent;

class DestroyAllTokenListener
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
    public function handle(DestroyAllTokenEvent $event): void
    {
        //
    }
}
