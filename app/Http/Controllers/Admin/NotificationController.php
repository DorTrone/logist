<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'customer' => ['nullable', 'integer', 'min:0'],
        ]);
        $f_customer = $request->has('customer') ? $request->customer : null;

        return view('admin.notification.index')
            ->with([
                'f_customer' => $f_customer,
            ]);
    }

    public function create()
    {
        return view('admin.notification.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:200'],
            'datetime' => ['required', 'date'],
        ]);

        $obj = new Notification();
        $obj->title = $request->title;
        $obj->body = $request->body;
        $obj->datetime = Carbon::parse($request->datetime)->startOfMinute();
        $obj->save();

        $pn = new PushNotification();
        $pn->notification_id = $obj->id;
        $pn->push = 'notification';
        $pn->to = 'shazada_app';
        $pn->title = $obj->title;
        $pn->body = str($obj->body)->limit(200);
        $pn->datetime = Carbon::parse($request->datetime)->startOfMinute();
        $pn->save();

        return to_route('admin.notifications.index')
            ->with([
                'success' => trans('app.notification') . ' ' . trans('app.added'),
            ]);
    }

    public function edit($id)
    {
        $obj = Notification::findOrFail($id);

        return view('admin.notification.edit')
            ->with([
                'obj' => $obj,
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:200'],
            'datetime' => ['required', 'date'],
        ]);

        $obj = Notification::findOrFail($id);
        $obj->title = $request->title;
        $obj->body = $request->body;
        $obj->datetime = Carbon::parse($request->datetime)->startOfMinute();
        $obj->update();

        PushNotification::where('notification_id', $obj->id)
            ->update([
                'push' => 'notification',
                'to' => 'shazada_app',
                'title' => $obj->title,
                'body' => str($obj->body)->limit(200),
                'datetime' => Carbon::parse($request->datetime)->startOfMinute(),
            ]);

        return to_route('admin.notifications.index')
            ->with([
                'success' => trans('app.notification') . ' ' . trans('app.updated'),
            ]);
    }

    public function destroy($id)
    {
        $obj = Notification::findOrFail($id);
        PushNotification::where('notification_id', $obj->id)
            ->delete();
        $obj->delete();

        return to_route('admin.notifications.index')
            ->with([
                'success' => trans('app.notification') . ' ' . trans('app.deleted'),
            ]);
    }

    public function api(Request $request)
    {
        $request->validate([
            'customer' => ['nullable', 'integer', 'min:0'],
        ]);
        $f_customer = $request->has('customer') ? $request->customer : null;

        $columns = [
            'id',
            'customer_id',
            'title',
            'body',
            'datetime',
            'created_at',
        ];
        $total = Notification::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Notification::when(isset($f_customer), function ($query) use ($f_customer) {
                if ($f_customer) {
                    return $query->where('customer_id', $f_customer);
                } else {
                    return $query->whereNull('customer_id');
                }
            })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('customer')
                ->get();
            $totalFiltered = Notification::when(isset($f_customer), function ($query) use ($f_customer) {
                if ($f_customer) {
                    return $query->where('customer_id', $f_customer);
                } else {
                    return $query->whereNull('customer_id');
                }
            })
                ->count();
        } else {
            $search = $request->input('search.value');
            $rs = Notification::when(isset($f_customer), function ($query) use ($f_customer) {
                if ($f_customer) {
                    return $query->where('customer_id', $f_customer);
                } else {
                    return $query->whereNull('customer_id');
                }
            })
                ->where(function ($query) use ($search) {
                    $query->where('id', 'ilike', "%{$search}%");
                    $query->orWhere('title', 'ilike', "%{$search}%");
                    $query->orWhere('body', 'ilike', "%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('customer')
                ->get();
            $totalFiltered = Notification::when(isset($f_customer), function ($query) use ($f_customer) {
                if ($f_customer) {
                    return $query->where('customer_id', $f_customer);
                } else {
                    return $query->whereNull('customer_id');
                }
            })
                ->where(function ($query) use ($search) {
                    $query->where('id', 'ilike', "%{$search}%");
                    $query->orWhere('title', 'ilike', "%{$search}%");
                    $query->orWhere('body', 'ilike', "%{$search}%");
                })
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['customer_id'] = isset($r->customer_id) ? $r->customer->getName() : '';
                $nestedData['title'] = $r->title;
                $nestedData['body'] = $r->body;
                $nestedData['datetime'] = $r->datetime->format('Y-m-d H:i:s');
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
                $nestedData['action'] = '<a href="' . route('admin.notifications.edit', $r->id) . '" class="btn btn-success btn-sm mb-1"><i class="bi-pencil"></i></a>';
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
