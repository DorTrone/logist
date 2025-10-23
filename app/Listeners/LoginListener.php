<?php

namespace App\Listeners;

use App\Models\AuthAttempt;
use App\Models\IpAddress;
use App\Models\UserAgent;
use Illuminate\Auth\Events\Login;

class LoginListener
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
    public function handle(Login $event): void
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
        $obj->username = $event->guard . ': ' . $event->user['username'];
        $obj->event = 'Login';
        $obj->save();
    }
}
