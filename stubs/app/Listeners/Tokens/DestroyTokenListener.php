<?php

namespace App\Listeners\Tokens;

use App\Events\Tokens\DestroyTokenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DestroyTokenListener
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
    public function handle(DestroyTokenEvent $event): void
    {
        //
    }
}
