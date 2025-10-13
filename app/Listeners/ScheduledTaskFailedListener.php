<?php

namespace App\Listeners;

use App\Models\Error;
use Illuminate\Console\Events\ScheduledTaskFailed;

class ScheduledTaskFailedListener
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
    public function handle(ScheduledTaskFailed $event): void
    {
        $title = 'Scheduled Task Failed';
        $body = $event->task->command . ' ' . now();

        if (app()->environment() === 'production') {
            Error::create(['title' => $title, 'body' => $body]);
        }
    }
}
