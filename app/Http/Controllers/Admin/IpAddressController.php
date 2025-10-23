<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpAddress;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpAddressController extends Controller
{
    public function index()
    {
        return view('admin.ipAddress.index');
    }

    public function disabled(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $obj = IpAddress::findOrFail($request->id);
        $obj->disabled = $obj->disabled ? 0 : 1;
        $obj->update();

        return response()->json([
            'status' => 1,
            'checked' => $obj->disabled,
        ], Response::HTTP_OK);
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'ip_address',
            'country_code',
            'country_name',
            'city_name',
            'disabled',
            'auth_attempts_count',
            'visitors_count',
        ];
        $total = IpAddress::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = IpAddress::withCount('authAttempts', 'visitors')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = IpAddress::count();
        } else {
            $search = $request->input('search.value');
            $rs = IpAddress::where('id', 'ilike', "%{$search}%")
                ->orWhere('ip_address', 'ilike', "%{$search}%")
                ->orWhere('country_code', 'ilike', "%{$search}%")
                ->orWhere('country_name', 'ilike', "%{$search}%")
                ->orWhere('city_name', 'ilike', "%{$search}%")
                ->withCount('authAttempts', 'visitors')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = IpAddress::where('id', 'ilike', "%{$search}%")
                ->orWhere('ip_address', 'ilike', "%{$search}%")
                ->orWhere('country_code', 'ilike', "%{$search}%")
                ->orWhere('country_name', 'ilike', "%{$search}%")
                ->orWhere('city_name', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['ip_address'] = $r->ip_address;
                $nestedData['country_code'] = $r->country_code;
                $nestedData['country_name'] = $r->country_name;
                $nestedData['city_name'] = $r->city_name;
                $nestedData['disabled'] = '<div class="form-check form-switch">'
                    . '<input class="form-check-input check-disabled" type="checkbox" role="switch" value="' . $r->id . '" id="check' . $r->id . '" ' . ($r->disabled ? 'checked' : '') . '>'
                    . '<label class="form-check-label" for="check' . $r->id . '">' . trans('app.disabled') . '</label>'
                    . '</div>';
                $nestedData['auth_attempts_count'] = '<a href="' . route('admin.authAttempts.index', ['ipAddress' => $r->id]) . '" class="fs-5 text-decoration-none ' . ($r->auth_attempts_count > 0 ? '' : 'd-none') . '">' . $r->auth_attempts_count . ' <i class="bi-box-arrow-up-right"></i></a>';
                $nestedData['visitors_count'] = '<a href="' . route('admin.visitors.index', ['ipAddress' => $r->id]) . '" class="fs-5 text-decoration-none ' . ($r->visitors_count > 0 ? '' : 'd-none') . '">' . $r->visitors_count . ' <i class="bi-box-arrow-up-right"></i></a>';
                $data[] = $nestedData;
            }
        }

        return json_encode([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($total),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }
}
