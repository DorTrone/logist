<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAgent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAgentController extends Controller
{
    public function index()
    {
        return view('admin.userAgent.index');
    }

    public function disabled(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $obj = UserAgent::findOrFail($request->id);
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
            'user_agent',
            'device',
            'platform',
            'browser',
            'robot',
            'disabled',
            'auth_attempts_count',
            'visitors_count',
        ];
        $total = UserAgent::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = UserAgent::withCount('authAttempts', 'visitors')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = UserAgent::count();
        } else {
            $search = $request->input('search.value');
            $rs = UserAgent::where('id', 'ilike', "%{$search}%")
                ->orWhere('user_agent', 'ilike', "%{$search}%")
                ->orWhere('device', 'ilike', "%{$search}%")
                ->orWhere('platform', 'ilike', "%{$search}%")
                ->orWhere('browser', 'ilike', "%{$search}%")
                ->orWhere('robot', 'ilike', "%{$search}%")
                ->withCount('authAttempts', 'visitors')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = UserAgent::where('id', 'ilike', "%{$search}%")
                ->orWhere('user_agent', 'ilike', "%{$search}%")
                ->orWhere('device', 'ilike', "%{$search}%")
                ->orWhere('platform', 'ilike', "%{$search}%")
                ->orWhere('browser', 'ilike', "%{$search}%")
                ->orWhere('robot', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['user_agent'] = $r->user_agent;
                $nestedData['device'] = $r->device;
                $nestedData['platform'] = $r->platform;
                $nestedData['browser'] = $r->browser;
                $nestedData['robot'] = $r->robot;
                $nestedData['disabled'] = '<div class="form-check form-switch">'
                    . '<input class="form-check-input check-disabled" type="checkbox" role="switch" value="' . $r->id . '" id="check' . $r->id . '" ' . ($r->disabled ? 'checked' : '') . '>'
                    . '<label class="form-check-label" for="check' . $r->id . '">' . trans('app.disabled') . '</label>'
                    . '</div>';
                $nestedData['auth_attempts_count'] = '<a href="' . route('admin.authAttempts.index', ['userAgent' => $r->id]) . '" class="fs-5 text-decoration-none ' . ($r->auth_attempts_count > 0 ? '' : 'd-none') . '">' . $r->auth_attempts_count . ' <i class="bi-box-arrow-up-right"></i></a>';
                $nestedData['visitors_count'] = '<a href="' . route('admin.visitors.index', ['userAgent' => $r->id]) . '" class="fs-5 text-decoration-none ' . ($r->visitors_count > 0 ? '' : 'd-none') . '">' . $r->visitors_count . ' <i class="bi-box-arrow-up-right"></i></a>';
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
