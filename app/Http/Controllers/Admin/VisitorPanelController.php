<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorPanelController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'api' => ['nullable', 'integer', 'between:0,1'],
            'robot' => ['nullable', 'integer', 'between:0,1'],
        ]);
        $api = $request->has('api') ? $request->api : 1;
        $robot = $request->has('robot') ? $request->robot : 0;

        $maps = DB::table("ip_addresses")
            ->leftJoin('visitors', function ($join) use ($api, $robot) {
                $join->on('ip_addresses.id', '=', 'visitors.ip_address_id')
                    ->where('visitors.api', $api)
                    ->where('visitors.robot', $robot)
                    ->where('visitors.updated_at', '>', today()->subWeek());
            })
            ->whereNotNull('ip_addresses.country_code')
            ->where('ip_addresses.country_code', "!=", '')
            ->havingRaw("COUNT(visitors.id) > ?", [0])
            ->selectRaw("COUNT(visitors.id) as count, ip_addresses.country_name, ip_addresses.country_code")
            ->groupBy('ip_addresses.country_name', 'ip_addresses.country_code')
            ->get();

        $days = Visitor::where('api', $api)
            ->where('robot', $robot)
            ->where('updated_at', '>', today()->firstOfMonth()->subYear())
            ->selectRaw("COUNT(id) as count, date_trunc('day', updated_at) as day") // DATE(updated_at) as day
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $months = Visitor::where('api', $api)
            ->where('robot', $robot)
            ->where('updated_at', '>', today()->firstOfMonth()->subYear())
            ->selectRaw("COUNT(id) as count, date_trunc('month', updated_at) as month")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $devices = Visitor::leftJoin('user_agents', 'user_agents.id', '=', 'visitors.user_agent_id')
            ->where('visitors.updated_at', '>', today()->firstOfMonth()->subYear())
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->selectRaw("user_agents.device as name, count(visitors.id) as count")
            ->whereNotNull('user_agents.device')
            ->groupBy('user_agents.device')
            ->orderBy('count', 'desc')
            ->take(20)
            ->get();

        $platforms = Visitor::leftJoin('user_agents', 'user_agents.id', '=', 'visitors.user_agent_id')
            ->where('visitors.updated_at', '>', today()->firstOfMonth()->subYear())
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->selectRaw("user_agents.platform as name, count(visitors.id) as count")
            ->whereNotNull('user_agents.platform')
            ->groupBy('user_agents.platform')
            ->orderBy('count', 'desc')
            ->take(20)
            ->get();

        $browsers = Visitor::leftJoin('user_agents', 'user_agents.id', '=', 'visitors.user_agent_id')
            ->where('visitors.updated_at', '>', today()->firstOfMonth()->subYear())
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->selectRaw("user_agents.browser as name, count(visitors.id) as count")
            ->whereNotNull('user_agents.browser')
            ->groupBy('user_agents.browser')
            ->orderBy('count', 'desc')
            ->take(20)
            ->get();

        $robots = Visitor::leftJoin('user_agents', 'user_agents.id', '=', 'visitors.user_agent_id')
            ->where('visitors.updated_at', '>', today()->firstOfMonth()->subYear())
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->selectRaw("user_agents.robot as name, count(visitors.id) as count")
            ->whereNotNull('user_agents.robot')
            ->groupBy('user_agents.robot')
            ->orderBy('count', 'desc')
            ->take(20)
            ->get();

        return view('admin.visitorPanel.index')
            ->with([
                'api' => $api,
                'robot' => $robot,
                'maps' => $maps,
                'days' => $days,
                'months' => $months,
                'devices' => $devices,
                'platforms' => $platforms,
                'browsers' => $browsers,
                'robots' => $robots,
            ]);
    }
}
