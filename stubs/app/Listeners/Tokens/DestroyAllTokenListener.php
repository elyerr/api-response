<?php

namespace App\Listeners\Tokens;

use App\Events\Tokens\DestroyAllTokenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
