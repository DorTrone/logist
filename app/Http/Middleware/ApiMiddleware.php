<?php

namespace App\Http\Middleware;

use App\Models\Error;
use App\Models\IpAddress;
use App\Models\UserAgent;
use App\Models\Visitor;
use Closure;
use Jenssegers\Agent\Agent;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->secure() and app()->environment() === 'production') {
            return response()->json([
                'status' => 0,
                'message' => 'Request blocked'
            ], Response::HTTP_NOT_FOUND);
        }
        if ($request->is('api/*')) {
            if ($request->token != '92612') {
                return response()->json([
                    'status' => 0,
                    'message' => 'Request blocked'
                ], Response::HTTP_NOT_FOUND);
            }
        }

        // IP ADDRESS
        // https://packagist.org/packages/geoip2/geoip2
        $reader = new Reader(storage_path('app/GeoLite2-City.mmdb'));
        $ip = $request->ip();
        try {
            $record = $reader->city($ip);
            $ip_address = IpAddress::firstOrCreate([
                'ip_address' => $ip,
                'country_code' => isset($record->country->isoCode) ? utf8_encode($record->country->isoCode) : null,
                'country_name' => isset($record->country->name) ? utf8_encode($record->country->name) : null,
                'city_name' => isset($record->city->name) ? utf8_encode($record->city->name) : null,
            ]);
        } catch (AddressNotFoundException $e) {
            $ip_address = IpAddress::firstOrCreate([
                'ip_address' => $ip,
            ]);
        }
        if ($ip_address->disabled) {
            return response()->json([
                'status' => 0,
                'message' => 'IP address disabled'
            ], Response::HTTP_NOT_FOUND);
        }

        // USER AGENT
        // https://github.com/jenssegers/agent
        $ua = $request->userAgent();
        $agent = new Agent();
        $agent->setUserAgent($ua);
        $user_agent = UserAgent::firstOrCreate([
            'user_agent' => $ua,
            'device' => $agent->device() ? str($agent->device())->substr(0, 255) : null,
            'platform' => $agent->platform() ? str($agent->platform())->substr(0, 255) : null,
            'browser' => $agent->browser() ? str($agent->browser())->substr(0, 255) : null,
            'robot' => $agent->robot() ? str($agent->robot())->substr(0, 255) : null,
        ]);
        if ($user_agent->disabled) {
            return response()->json([
                'status' => 0,
                'message' => 'UserAgent disabled'
            ], Response::HTTP_NOT_FOUND);
        }
        $robot = $agent->isRobot();
        if ($robot) {
            return response()->json([
                'status' => 0,
                'message' => 'UserAgent robot'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $v = Visitor::where('ip_address_id', $ip_address->id)
                ->where('user_agent_id', $user_agent->id)
                ->where('updated_at', '>=', today())
                ->whereApi(1)
                ->whereRobot($robot)
                ->firstOrFail();
            if ($v->suspect_hits > 500) {
                if (!$v->disabled) {
                    $v->disabled = 1;
                    $v->update();
                    Error::firstOrCreate([
                        'title' => 'Api Visitor blocked, ID: ' . $v->id,
                        'body' => 'IpAddress: ' . $request->ip() . ', UserAgent: ' . $request->userAgent(),
                    ]);
                }
                return response()->json([
                    'status' => 0,
                    'message' => 'Visitor disabled'
                ], Response::HTTP_NOT_FOUND);
            }
            if ($v->disabled) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Visitor disabled'
                ], Response::HTTP_NOT_FOUND);
            }
            if ($v->updated_at == now()) {
                $v->increment('suspect_hits');
            }
            $v->increment('hits');
        } catch (ModelNotFoundException $e) {
            $obj = new Visitor();
            $obj->ip_address_id = $ip_address->id;
            $obj->user_agent_id = $user_agent->id;
            $obj->api = 1;
            $obj->robot = $robot;
            $obj->save();
        }

        if (auth('api')->check()) {
            $user = auth('api')->user();
            $user->last_seen = now();
            $user->update();

            if (!in_array(2, $user['guards'] ?: [])) {
                return response()->json([
                    'status' => 0,
                ], Response::HTTP_FORBIDDEN);
            }
        }

        if (auth('customer_api')->check()) {
            $user = auth('customer_api')->user();
            $user->last_seen = now();
            $user->update();
        }

        return $next($request);
    }
}
