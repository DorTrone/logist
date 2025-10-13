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

class WebMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ====================
        // IP ADDRESS
        // ====================
        $reader = new Reader(storage_path('app/GeoLite2-City.mmdb'));
        $ip = $request->ip();
        try {
            $record = $reader->city($ip);
            $ip_address = IpAddress::firstOrCreate([
                'ip_address'   => $ip,
                'country_code' => isset($record->country->isoCode) ? utf8_encode($record->country->isoCode) : null,
                'country_name' => isset($record->country->name) ? utf8_encode($record->country->name) : null,
                'city_name'    => isset($record->city->name) ? utf8_encode($record->city->name) : null,
            ]);
        } catch (AddressNotFoundException $e) {
            $ip_address = IpAddress::firstOrCreate([
                'ip_address' => $ip,
            ]);
        }

        if ($ip_address->disabled) {
            abort(404);
        }

        // ====================
        // USER AGENT
        // ====================
        $ua = $request->userAgent();
        $agent = new Agent();
        $agent->setUserAgent($ua);

        $user_agent = UserAgent::firstOrCreate([
            'user_agent' => $ua,
            'device'     => $agent->device() ? str($agent->device())->substr(0, 255) : null,
            'platform'   => $agent->platform() ? str($agent->platform())->substr(0, 255) : null,
            'browser'    => $agent->browser() ? str($agent->browser())->substr(0, 255) : null,
            'robot'      => $agent->robot() ? str($agent->robot())->substr(0, 255) : null,
        ]);

        if ($user_agent->disabled || $agent->isRobot()) {
            abort(404);
        }

        // ====================
        // VISITOR TRACKING
        // ====================
        $robot = $agent->isRobot();
        try {
            $v = Visitor::where('ip_address_id', $ip_address->id)
                ->where('user_agent_id', $user_agent->id)
                ->where('updated_at', '>=', today())
                ->whereApi(0)
                ->whereRobot($robot)
                ->firstOrFail();

            if ($v->suspect_hits > 500) {
                if (!$v->disabled) {
                    $v->disabled = 1;
                    $v->update();
                    Error::firstOrCreate([
                        'title' => 'Web Visitor blocked, ID: ' . $v->id,
                        'body'  => 'IpAddress: ' . $request->ip() . ', UserAgent: ' . $request->userAgent(),
                    ]);
                }
                abort(404);
            }

            if ($v->disabled) {
                abort(404);
            }

            if ($v->updated_at == now()) {
                $v->increment('suspect_hits');
            }
            $v->increment('hits');
        } catch (ModelNotFoundException $e) {
            $obj = new Visitor();
            $obj->ip_address_id = $ip_address->id;
            $obj->user_agent_id = $user_agent->id;
            $obj->api = 0;
            $obj->robot = $robot;
            $obj->save();
        }

        // ====================
        // AUTH CHECK
        // ====================
        if (auth()->check()) {
            $user = auth()->user();
            $user->last_seen = now();
            $user->update();

            // ====================
            // Обработка guards
            // ====================
            $rawGuards = $user->guards;

            if (is_string($rawGuards)) {
                // Чистим лишние кавычки и декодируем JSON
                $rawGuards = trim($rawGuards, '"');
                $decoded = json_decode($rawGuards, true);
                $guards = is_array($decoded) ? array_map('intval', $decoded) : [];
            } elseif (is_array($rawGuards)) {
                $guards = array_map('intval', $rawGuards);
            } else {
                $guards = [];
            }

            // Проверка доступа к админ-панели (id 1)
            if (!in_array(1, $guards)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
