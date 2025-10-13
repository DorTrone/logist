<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customer.index');
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'alpha_num', 'max:50', Rule::unique('customers')],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $obj = new Customer();
        $obj->code = str()->random(5);
        $obj->name = $request->name;
        $obj->surname = $request->surname;
        $obj->username = $request->username;
        $obj->password = bcrypt($request->password);
        $obj->note = $request->note ?: null;
        $obj->auth_method = 2;
        $obj->save();

        $obj->code = 'SZD' . strval($obj->id + 1000);
        $obj->ext_keyword = str(strval($obj->id + 1000)
            . ' SZD' . strval($obj->id + 1000)
            . ' ' . $obj->name
            . ' ' . $obj->surname
            . ' ' . $obj->username)->squish()->lower()->slug(' ');
        $obj->update();

        return to_route('admin.customers.index')
            ->with([
                'success' => trans('app.customer') . ' ' . trans('app.added'),
            ]);
    }

    public function edit($id)
    {
        $obj = Customer::withTrashed()
            ->findOrFail($id);

        return view('admin.customer.edit')
            ->with([
                'obj' => $obj,
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $obj = Customer::withTrashed()
            ->findOrFail($id);
        $obj->name = $request->name;
        $obj->surname = $request->surname;
        if ($obj->auth_method == 2) {
            $request->validate([
                'username' => ['required', 'string', 'max:50', Rule::unique('customers')->ignore($id)],
            ]);
            $obj->username = $request->username;
        }
        if (isset($request->password)) {
            $obj->password = bcrypt($request->password);
        }
        $obj->note = $request->note ?: null;
        $obj->update();

        $obj->ext_keyword = str(strval($obj->id + 1000)
            . ' SZD' . strval($obj->id + 1000)
            . ' ' . $obj->name
            . ' ' . $obj->surname
            . ' ' . $obj->username)->squish()->lower()->slug(' ');
        $obj->update();

        return to_route('admin.customers.index')
            ->with([
                'success' => trans('app.customer') . ' ' . trans('app.updated'),
            ]);
    }

    public function destroy($id)
    {
        $obj = Customer::withTrashed()
            ->withCount('packages')
            ->findOrFail($id);
        if ($obj->packages_count > 0) {
            return redirect()->back()
                ->with([
                    'error' => trans('app.error')
                        . ', ' . trans('app.packagesCount') . ': ' . $obj->packages_count
                ]);
        }
        DB::table('notifications')
            ->where('customer_id', $obj->id)
            ->delete();
        $obj->forceDelete();

        return to_route('admin.customers.index')
            ->with([
                'success' => trans('app.customer') . ' ' . trans('app.deleted'),
            ]);
    }

    public function api(Request $request)
    {
        $columns = [
            'id',
            'code',
            'auth_method',
            'platform',
            'language',
            'name',
            'surname',
            'username',
            'note',
            'last_seen',
            'created_at',
            'packages_count',
            'notifications_count',
        ];
        $total = Customer::withTrashed()
            ->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Customer::withTrashed()
                ->withCount('packages', 'notifications')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Customer::withTrashed()
                ->count();
        } else {
            $search = $request->input('search.value');
            $searchTerm = str($search)->squish()->lower()->slug(' ');
            $words = preg_split('/\s+/', $searchTerm);
            $filteredWords = array_filter(array_map(function ($word) {
                return preg_replace('/[^a-zA-Z0-9]/', '', $word);
            }, $words));
            $searchQuery = implode(' & ', array_map(function ($word) {
                return $word . ':*';
            }, $filteredWords));
            $rs = Customer::withTrashed()
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->withCount('packages', 'notifications')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->get();
            $totalFiltered = Customer::withTrashed()
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['code'] = '<div class="font-monospace text-danger fw-semibold">' . $r->code . '</div>';
                $nestedData['auth_method'] = '<i class="bi-' . $r->authMethodIcon() . ' text-secondary"></i> ' . $r->authMethod();
                $nestedData['platform'] = '<i class="bi-' . $r->platformIcon() . ' text-secondary"></i> ' . $r->platform();
                $nestedData['language'] = $r->language();
                $nestedData['name'] = $r->name;
                $nestedData['surname'] = $r->surname;
                $nestedData['username'] = $r->username;
                $nestedData['note'] = $r->note;
                $nestedData['last_seen'] = $r->last_seen ? '<a href="' . route('admin.tokens.index', ['tokenType' => 1, 'tokenId' => $r->id]) . '" class="link-dark text-decoration-none">' . $r->last_seen->format('Y-m-d H:i:s') . '</a>' : '';
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
                $nestedData['packages_count'] = '<a href="' . route('admin.packages.index', ['customers' => [$r->id]]) . '" class="fs-5 text-decoration-none ' . ($r->packages_count > 0 ? '' : 'd-none') . '">' . $r->packages_count . ' <i class="bi-box-arrow-up-right"></i></a>';
                $nestedData['notifications_count'] = '<a href="' . route('admin.notifications.index', ['customer' => $r->id]) . '" class="fs-5 text-decoration-none ' . ($r->notifications_count > 0 ? '' : 'd-none') . '">' . $r->notifications_count . ' <i class="bi-box-arrow-up-right"></i></a>';
                $nestedData['action'] = '<a href="' . route('admin.customers.edit', $r->id) . '" class="btn btn-success btn-sm mb-1"><i class="bi-pencil"></i></a>';
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
