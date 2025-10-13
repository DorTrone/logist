<?php

namespace App\Listeners;

use App\Models\AuthAttempt;
use App\Models\Error;
use App\Models\IpAddress;
use App\Models\UserAgent;
use Illuminate\Auth\Events\Failed;

class FailedListener
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
    public function handle(Failed $event): void
    {
        $ip_address = IpAddress::where('ip_address', request()->ip())
            ->orderBy('id', 'desc')
            ->firstOrFail();
        $user_agent = UserAgent::where('user_agent', request()->userAgent())
            ->orderBy('id', 'desc')
            ->firstOrFail();

        $obj = new AuthAttempt();
        $obj->ip_address_id = $ip_address->id;
        $obj->user_agent_id = $user_agent->id;
        $obj->username = $event->guard . ': ' . ($event->user ? $event->user['username'] : 'Not found');
        $obj->event = 'Failed';
        $obj->save();

        if ($event->guard === 'web') {
            $title = $obj->event . ': ' . $obj->username;
            $body = 'IP: ' . $ip_address->ip_address . ', UserAgent: ' . $user_agent->user_agent;

            if (app()->environment() === 'production') {
                Error::create(['title' => $title, 'body' => $body]);
            }
        }
    }
}
