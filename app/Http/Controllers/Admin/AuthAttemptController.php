<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthAttempt;
use Illuminate\Http\Request;

class AuthAttemptController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'ipAddress' => ['nullable', 'integer', 'min:1'],
            'userAgent' => ['nullable', 'integer', 'min:1'],
        ]);
        $f_ipAddress = $request->ipAddress ?: null;
        $f_userAgent = $request->userAgent ?: null;

        return view('admin.authAttempt.index')
            ->with([
                'f_ipAddress' => $f_ipAddress,
                'f_userAgent' => $f_userAgent,
            ]);
    }

    public function api(Request $request)
    {
        $request->validate([
            'ipAddress' => ['nullable', 'integer', 'min:1'],
            'userAgent' => ['nullable', 'integer', 'min:1'],
        ]);
        $f_ipAddress = $request->ipAddress ?: null;
        $f_userAgent = $request->userAgent ?: null;

        $columns = [
            'id',
            'ip_address_id',
            'user_agent_id',
            'username',
            'event',
            'created_at',
        ];
        $total = AuthAttempt::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = AuthAttempt::when(isset($f_ipAddress), function ($query) use ($f_ipAddress) {
                return $query->where('ip_address_id', $f_ipAddress);
            })
                ->when(isset($f_userAgent), function ($query) use ($f_userAgent) {
                    return $query->where('user_agent_id', $f_userAgent);
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('ipAddress', 'userAgent')
                ->get();
            $totalFiltered = AuthAttempt::when(isset($f_ipAddress), function ($query) use ($f_ipAddress) {
                return $query->where('ip_address_id', $f_ipAddress);
            })
                ->when(isset($f_userAgent), function ($query) use ($f_userAgent) {
                    return $query->where('user_agent_id', $f_userAgent);
                })
                ->count();
        } else {
            $search = $request->input('search.value');
            $rs = AuthAttempt::when(isset($f_ipAddress), function ($query) use ($f_ipAddress) {
                return $query->where('ip_address_id', $f_ipAddress);
            })
                ->when(isset($f_userAgent), function ($query) use ($f_userAgent) {
                    return $query->where('user_agent_id', $f_userAgent);
                })
                ->where(function ($query) use ($search) {
                    $query->where('id', 'ilike', "%{$search}%");
                    $query->orWhere('username', 'ilike', "%{$search}%");
                    $query->orWhere('event', 'ilike', "%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('ipAddress', 'userAgent')
                ->get();
            $totalFiltered = AuthAttempt::when(isset($f_ipAddress), function ($query) use ($f_ipAddress) {
                return $query->where('ip_address_id', $f_ipAddress);
            })
                ->when(isset($f_userAgent), function ($query) use ($f_userAgent) {
                    return $query->where('user_agent_id', $f_userAgent);
                })
                ->where(function ($query) use ($search) {
                    $query->where('id', 'ilike', "%{$search}%");
                    $query->orWhere('username', 'ilike', "%{$search}%");
                    $query->orWhere('event', 'ilike', "%{$search}%");
                })
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['ip_address_id'] = '<span class="text-primary">' . $r->ip_address_id . '</span>: ' . $r->ipAddress->ip_address
                    . ($r->ipAddress->disabled ? ' <span class="badge bg-danger-subtle text-danger-emphasis">' . trans('app.disabled') . '</span>' : ' <span class="badge bg-success-subtle text-success-emphasis">' . trans('app.active') . '</span>')
                    . '<div class="small text-secondary">' . $r->ipAddress->ip() . '</div>';
                $nestedData['user_agent_id'] = '<span class="text-primary">' . $r->user_agent_id . '</span>: ' . $r->userAgent->ua()
                    . ($r->userAgent->disabled ? ' <span class="badge bg-danger-subtle text-danger-emphasis">' . trans('app.disabled') . '</span>' : ' <span class="badge bg-success-subtle text-success-emphasis">' . trans('app.active') . '</span>')
                    . '<div class="small text-secondary">' . $r->userAgent->user_agent . '</div>';
                $nestedData['username'] = $r->username;
                $nestedData['event'] = $r->event;
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
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
