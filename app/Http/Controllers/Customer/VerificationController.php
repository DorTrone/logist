<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\VerificationMail;
use App\Models\Error;
use App\Models\PushNotification;
use App\Models\Verification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'auth_method' => ['required', 'integer', 'between:0,1'],
        ]);
        $validator->sometimes('username', ['integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'], function (Fluent $input) {
            return $input->auth_method == 0;
        });
        $validator->sometimes('username', ['string', 'email', 'max:50'], function (Fluent $input) {
            return $input->auth_method == 1;
        });
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Verification verify 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = Verification::where('username', $request->username)
            ->whereIn('status', [0, 1, 3])
            ->where('method', $request->auth_method)
            ->where('updated_at', '>', now()->subMinutes(2))
            ->orderBy('id', 'desc')
            ->first();

        if ($obj) {
            $obj->update();
        } else {
            $obj = Verification::updateOrCreate([
                'username' => $request->username,
                'method' => $request->auth_method,
            ], [
                'code' => rand(10000, 99999),
                'status' => 0,
            ]);
        }

        //

        if ($obj->method == 0) {
            $to = 'shazada_sms';
            $title = $obj->username;
            $body = strval($obj->code);
            $push = [
                'credentials' => storage_path('app/fsa_sms.json'),
                'id' => 'sms-service-2024',
                'message' => [
                    'topic' => $to,
                    'notification' => ['title' => $title, 'body' => $body],
                    'android' => ['priority' => 'high', 'notification' => ['sound' => 'default', 'default_sound' => true]],
                    'apns' => ['headers' => ['apns-priority' => '10'], 'payload' => ['aps' => ['badge' => 1, 'sound' => 'default']]],
                ],
            ];

            $attempt = 0;
            do {
                try {
                    if (app()->environment() === 'production') {
                        $client = new \Google\Client();
                        $client->setAuthConfig($push['credentials']);
                        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

                        $token = $client->fetchAccessTokenWithAssertion();
                        $accessToken = $token['access_token'];

                        $client = new \GuzzleHttp\Client();
                        $response = $client->post('https://fcm.googleapis.com/v1/projects/' . $push['id'] . '/messages:send', [
                            'headers' => ['Authorization' => 'Bearer ' . $accessToken, 'Content-Type' => 'application/json'],
                            'json' => ['message' => $push['message']],
                        ]);
                    }
                    DB::table('errors')
                        ->where('title', 'Verification ID: ' . $obj->id)
                        ->update(['attempts' => $attempt, 'status' => 1]);

                    $obj->status = 1;
                    $obj->update();

                    break;
                } catch (Exception $e) {
                    Error::firstOrCreate([
                        'title' => 'Verification ID: ' . $obj->id,
                        'body' => $e->getMessage(),
                    ]);
                    DB::table('errors')
                        ->where('title', 'Verification ID: ' . $obj->id)
                        ->update(['attempts' => $attempt, 'status' => 2]);

                    $obj->status = 3;
                    $obj->update();

                    $attempt++;
                    sleep(2);
                }
            } while ($attempt < 10);

            if (in_array($obj->status, [0, 3])) {
                $pn = new PushNotification();
                $pn->push = 'sms';
                $pn->to = 'shazada_sms';
                $pn->title = $obj->username;
                $pn->body = $obj->code;
                $pn->datetime = now()->addMinute()->startOfMinute();
                $pn->save();
            }
        } else {
            Mail::to($obj->username)->send(new VerificationMail($obj->code));
        }

        return response()->json([
            'status' => 1,
        ], Response::HTTP_OK);
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'code' => ['required', 'integer', 'between:10000,99999'],
            'auth_method' => ['required', 'integer', 'between:0,1'],
        ]);
        $validator->sometimes('username', ['integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'], function (Fluent $input) {
            return $input->auth_method == 0;
        });
        $validator->sometimes('username', ['string', 'email', 'max:50'], function (Fluent $input) {
            return $input->auth_method == 1;
        });
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Verification confirm 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = Verification::where('username', $request->username)
            ->where('code', $request->code)
            ->whereIn('status', [0, 1, 3])
            ->where('method', $request->auth_method)
            ->where('updated_at', '>', now()->subMinutes(2))
            ->orderBy('id', 'desc')
            ->first();

        if ($obj) {
            return response()->json([
                'status' => 1,
            ], Response::HTTP_OK);
        } else {
            Error::create([
                'title' => 'Customer Verification confirm 422',
                'body' => 'Invalid verification code',
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Invalid verification code',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
