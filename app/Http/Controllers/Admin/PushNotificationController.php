<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function index()
    {
        return view('admin.pushNotification.index');
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'notification_id',
            'push',
            'to',
            'title',
            'body',
            'datetime',
        ];
        $total = PushNotification::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = PushNotification::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = PushNotification::count();
        } else {
            $search = $request->input('search.value');
            $rs = PushNotification::where('id', 'ilike', "%{$search}%")
                ->orWhere('push', 'ilike', "%{$search}%")
                ->orWhere('to', 'ilike', "%{$search}%")
                ->orWhere('title', 'ilike', "%{$search}%")
                ->orWhere('body', 'ilike', "%{$search}%")
                ->orWhere('datetime', 'ilike', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = PushNotification::where('id', 'ilike', "%{$search}%")
                ->orWhere('push', 'ilike', "%{$search}%")
                ->orWhere('to', 'ilike', "%{$search}%")
                ->orWhere('title', 'ilike', "%{$search}%")
                ->orWhere('body', 'ilike', "%{$search}%")
                ->orWhere('datetime', 'ilike', "%{$search}%")
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['notification_id'] = $r->notification_id;
                $nestedData['push'] = $r->push;
                $nestedData['to'] = $r->to;
                $nestedData['title'] = $r->title;
                $nestedData['body'] = $r->body;
                $nestedData['datetime'] = $r->datetime->format('Y-m-d H:i:s');
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
