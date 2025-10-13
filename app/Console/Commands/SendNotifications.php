<?php

namespace App\Console\Commands;

use App\Models\Error;
use App\Models\PushNotification;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pns = PushNotification::where('datetime', '=', now()->startOfMinute())
            ->orderBy('id')
            ->get();

        foreach ($pns as $pn) {
            $to = $pn->to;
            $title = $pn->title;
            $body = $pn->body;

            $push = [
                'sms' => [
                    'credentials' => storage_path('app/fsa_sms.json'),
                    'id' => 'sms-service-2024',
                    'message' => [
                        'topic' => $to,
                        'notification' => ['title' => $title, 'body' => $body],
                        'android' => ['priority' => 'high', 'notification' => ['sound' => 'default', 'default_sound' => true]],
                        'apns' => ['headers' => ['apns-priority' => '10'], 'payload' => ['aps' => ['badge' => 1, 'sound' => 'default']]],
                    ],
                ],
                'notification' => [
                    'credentials' => storage_path('app/fsa_app.json'),
                    'id' => 'shazada-client',
                    'message' => [
                        'topic' => $to,
                        'notification' => ['title' => $title, 'body' => $body],
                        'android' => ['priority' => 'high', 'notification' => ['sound' => 'default', 'default_sound' => true]],
                        'apns' => ['headers' => ['apns-priority' => '10'], 'payload' => ['aps' => ['badge' => 1, 'sound' => 'default']]],
                        'data' => ['page' => 'notification'],
                    ],
                ],
                'app' => [
                    'credentials' => storage_path('app/fsa_app.json'),
                    'id' => 'shazada-client',
                    'message' => [
                        'topic' => $to,
                        'notification' => ['title' => $title, 'body' => $body],
                        'android' => ['priority' => 'high', 'notification' => ['sound' => 'default', 'default_sound' => true]],
                        'apns' => ['headers' => ['apns-priority' => '10'], 'payload' => ['aps' => ['badge' => 1, 'sound' => 'default']]],
                    ],
                ],
            ];

            $attempt = 0;
            do {
                try {
                    if (app()->environment() === 'production') {
                        $client = new \Google\Client();
                        $client->setAuthConfig($push[$pn->push]['credentials']);
                        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

                        $token = $client->fetchAccessTokenWithAssertion();
                        $accessToken = $token['access_token'];

                        $client = new \GuzzleHttp\Client();
                        $response = $client->post('https://fcm.googleapis.com/v1/projects/' . $push[$pn->push]['id'] . '/messages:send', [
                            'headers' => ['Authorization' => 'Bearer ' . $accessToken, 'Content-Type' => 'application/json'],
                            'json' => ['message' => $push[$pn->push]['message']],
                        ]);
                    }
                    DB::table('errors')
                        ->where('title', 'PushNotification ID: ' . $pn->id)
                        ->update(['attempts' => $attempt, 'status' => 1]);

                    break;
                } catch (Exception $e) {
                    Error::firstOrCreate([
                        'title' => 'PushNotification ID: ' . $pn->id,
                        'body' => $e->getMessage(),
                    ]);
                    DB::table('errors')
                        ->where('title', 'PushNotification ID: ' . $pn->id)
                        ->update(['attempts' => $attempt, 'status' => 2]);

                    $attempt++;
                    sleep(2);
                }
            } while ($attempt < 10);
        }

        return Command::SUCCESS;
    }
}
