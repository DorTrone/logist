<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Verification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        return view('admin.verification.index');
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'username',
            'code',
            'method',
            'status',
            'created_at',
            'updated_at',
        ];
        $total = Verification::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Verification::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Verification::count();
        } else {
            $search = $request->input('search.value');
            $rs = Verification::where('id', 'ilike', "%{$search}%")
                ->orWhere('username', 'ilike', "%{$search}%")
                ->orWhere('code', 'ilike', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Verification::where('id', 'ilike', "%{$search}%")
                ->orWhere('username', 'ilike', "%{$search}%")
                ->orWhere('code', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['username'] = '<i class="bi-person-bounding-box text-secondary"></i> ' . $r->username;
                $nestedData['code'] = '<i class="bi-lock-fill text-secondary"></i> ' . $r->code;
                $nestedData['method'] = $r->method();
                $nestedData['status'] = '<div class="badge bg-' . $r->statusColor() . '-subtle text-' . $r->statusColor() . '-emphasis">' . $r->status() . '</div>';
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
                $nestedData['updated_at'] = $r->updated_at->format('Y-m-d H:i:s');
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
