<?php

namespace App\Http\Middleware;

use App\Models\IpAddress;
use App\Models\UserAgent;
use App\Models\Visitor;
use Closure;
use Jenssegers\Agent\Agent;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Получаем IP
            $ip = $request->ip();
            
            // Кешируем IP address на 1 час
            $ipAddress = Cache::remember("ip_address:{$ip}", 3600, function () use ($ip) {
                return $this->getOrCreateIpAddress($ip);
            });

            // Получаем User Agent
            $ua = $request->userAgent();
            $agent = new Agent();
            $agent->setUserAgent($ua);

            // Кешируем User Agent на 1 час
            $cacheKey = 'user_agent:' . md5($ua);
            $userAgent = Cache::remember($cacheKey, 3600, function () use ($ua, $agent) {
                return UserAgent::firstOrCreate([
                    'user_agent' => $ua,
                ], [
                    'device'   => $agent->device() ? str($agent->device())->substr(0, 255) : null,
                    'platform' => $agent->platform() ? str($agent->platform())->substr(0, 255) : null,
                    'browser'  => $agent->browser() ? str($agent->browser())->substr(0, 255) : null,
                    'robot'    => $agent->robot() ? str($agent->robot())->substr(0, 255) : null,
                ]);
            });

            // Трекаем визит
            $this->trackVisit($ipAddress, $userAgent, $agent->isRobot());

            // Сохраняем в request для использования в других middleware
            $request->merge([
                '_ip_address' => $ipAddress,
                '_user_agent' => $userAgent,
            ]);

        } catch (\Exception $e) {
            // Логируем ошибку, но не блокируем запрос
            \Log::error('Visitor tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Get or create IP address with GeoIP data
     */
    private function getOrCreateIpAddress(string $ip): IpAddress
    {
        $geoipPath = storage_path('app/GeoLite2-City.mmdb');
        
        if (!file_exists($geoipPath)) {
            // Если нет GeoIP базы, создаем без геоданных
            return IpAddress::firstOrCreate(['ip_address' => $ip]);
        }

        try {
            $reader = new Reader($geoipPath);
            $record = $reader->city($ip);
            
            return IpAddress::firstOrCreate([
                'ip_address' => $ip,
            ], [
                'country_code' => $record->country->isoCode ?? null,
                'country_name' => $record->country->name ?? null,
                'city_name'    => $record->city->name ?? null,
            ]);
        } catch (AddressNotFoundException $e) {
            return IpAddress::firstOrCreate(['ip_address' => $ip]);
        }
    }

    /**
     * Track visitor
     */
    private function trackVisit(IpAddress $ipAddress, UserAgent $userAgent, bool $isRobot): void
    {
        try {
            $visitor = Visitor::where('ip_address_id', $ipAddress->id)
                ->where('user_agent_id', $userAgent->id)
                ->where('updated_at', '>=', today())
                ->where('api', 0)
                ->where('robot', $isRobot)
                ->first();

            if ($visitor) {
                if ($visitor->updated_at->isToday()) {
                    $visitor->increment('suspect_hits');
                }
                $visitor->increment('hits');
            } else {
                Visitor::create([
                    'ip_address_id' => $ipAddress->id,
                    'user_agent_id' => $userAgent->id,
                    'api'           => 0,
                    'robot'         => $isRobot,
                    'hits'          => 1,
                    'suspect_hits'  => 0,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Visitor tracking failed: ' . $e->getMessage());
        }
    }
}
