<?php

namespace App\Listeners;

use App\Models\Error;
use Illuminate\Http\Client\Events\ConnectionFailed;

class ConnectionFailedListener
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
    public function handle(ConnectionFailed $event): void
    {
        $title = 'Connection Failed';
        $body = $event->request->url() . ' ' . now();

        if (app()->environment() === 'production') {
            Error::create(['title' => $title, 'body' => $body]);
        }
    }
}
