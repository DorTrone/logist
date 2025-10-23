<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Error;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function index()
    {
        return view('admin.error.index');
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'title',
            'body',
            'attempts',
            'status',
            'created_at',
            'updated_at',
        ];
        $total = Error::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Error::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Error::count();
        } else {
            $search = $request->input('search.value');
            $rs = Error::where('id', 'ilike', "%{$search}%")
                ->orWhere('title', 'ilike', "%{$search}%")
                ->orWhere('body', 'ilike', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Error::where('id', 'ilike', "%{$search}%")
                ->orWhere('title', 'ilike', "%{$search}%")
                ->orWhere('body', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['title'] = $r->title;
                $nestedData['body'] = $r->body;
                $nestedData['attempts'] = $r->attempts;
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
