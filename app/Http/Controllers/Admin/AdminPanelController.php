<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPanelController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'api' => ['nullable', 'integer', 'between:0,1'],
            'robot' => ['nullable', 'integer', 'between:0,1'],
        ]);
        $api = $request->has('api') ? $request->api : 1;
        $robot = $request->has('robot') ? $request->robot : 0;

        $suspectVisitors = Visitor::where('api', $api)
            ->where('robot', $robot)
            ->where('suspect_hits', '>', 50)
            ->orderBy('updated_at', 'desc')
            ->take(100)
            ->with('ipAddress', 'userAgent')
            ->get();

        $countries = DB::table('ip_addresses')
            ->leftJoin('visitors', 'ip_addresses.id', '=', 'visitors.ip_address_id')
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->selectRaw("COUNT(visitors.id) as count, ip_addresses.country_name, ip_addresses.country_code")
            ->groupBy('ip_addresses.country_name', 'ip_addresses.country_code')
            ->orderBy('count', 'desc')
            ->take(100)
            ->get();

        $suspectCountries = DB::table('ip_addresses')
            ->leftJoin('visitors', 'ip_addresses.id', '=', 'visitors.ip_address_id')
            ->where('visitors.api', $api)
            ->where('visitors.robot', $robot)
            ->where('visitors.suspect_hits', '>', 50)
            ->selectRaw("COUNT(visitors.id) as count, ip_addresses.country_name, ip_addresses.country_code")
            ->groupBy('ip_addresses.country_name', 'ip_addresses.country_code')
            ->orderBy('count', 'desc')
            ->take(100)
            ->get();

        return view('admin.adminPanel.index')
            ->with([
                'api' => $api,
                'robot' => $robot,
                'suspectVisitors' => $suspectVisitors,
                'countries' => $countries,
                'suspectCountries' => $suspectCountries,
            ]);
    }
}
