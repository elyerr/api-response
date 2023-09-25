<?php

namespace App\Listeners\Tokens;

use App\Events\Tokens\StoreTokenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
