<?php

use App\Console\Commands\RemainingDeparture;
use App\Console\Commands\SendNotifications;
use App\Console\Commands\StandardDeparture;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendNotifications::class)
    ->everyMinute()
    ->runInBackground();

Schedule::command(StandardDeparture::class)
    ->cron('0 19 * * 4')
    ->runInBackground();

Schedule::command(RemainingDeparture::class)
    ->cron('0 19 * * 6')
    ->runInBackground();
