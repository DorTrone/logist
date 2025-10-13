<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $objs = User::when(auth()->id() != 1, fn($query) => $query->where('id', '!=', 1))
            ->orderBy('username')
            ->orderBy('id', 'desc')
            ->with('tasks')
            ->get();

        return view('admin.user.index')
            ->with([
                'objs' => $objs,
                'guards' => $this->getGuards(),
                'permissions' => $this->getPermissions(),
                'api_permissions' => $this->getApiPermissions(),
                'queries' => [
                    'locations' => config('const.locations'),
                    'transportTypes' => config('const.transportTypes'),
                    'packagePayments' => config('const.packagePayments'),
                    'packageTypes' => config('const.packageTypes'),
                    'packageStatuses' => config('const.packageStatuses'),
                    'paymentStatuses' => config('const.paymentStatuses'),
                ],
            ]);
    }

    public function create()
    {
        $tasks = Task::orderBy('name')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.user.create')
            ->with([
                'tasks' => $tasks,
                'guards' => $this->getGuards(),
                'permissions' => $this->getPermissions(),
                'api_permissions' => $this->getApiPermissions(),
                'queries' => [
                    'locations' => config('const.locations'),
                    'transportTypes' => config('const.transportTypes'),
                    'packagePayments' => config('const.packagePayments'),
                    'packageTypes' => config('const.packageTypes'),
                    'packageStatuses' => config('const.packageStatuses'),
                    'paymentStatuses' => config('const.paymentStatuses'),
                ],
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['nullable', 'integer', 'min:1'],
            'guards' => ['nullable', 'array'],
            'guards.*' => ['nullable', 'integer', 'min:1'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'integer', 'min:1'],
            'api_permissions' => ['nullable', 'array'],
            'api_permissions.*' => ['nullable', 'integer', 'min:1'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['nullable', 'integer', 'between:0,5'],
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'packagePayments' => ['nullable', 'array'],
            'packagePayments.*' => ['nullable', 'integer', 'between:0,2'],
            'packageTypes' => ['nullable', 'array'],
            'packageTypes.*' => ['nullable', 'integer', 'between:0,6'],
            'packageStatuses' => ['nullable', 'array'],
            'packageStatuses.*' => ['nullable', 'integer', 'between:0,5'],
            'paymentStatuses' => ['nullable', 'array'],
            'paymentStatuses.*' => ['nullable', 'integer', 'between:0,3'],
        ]);
        $f_locations = $request->has('locations') ? $request->locations : [];
        $f_transportTypes = $request->has('transportTypes') ? $request->transportTypes : [];
        $f_packagePayments = $request->has('packagePayments') ? $request->packagePayments : [];
        $f_packageTypes = $request->has('packageTypes') ? $request->packageTypes : [];
        $f_packageStatuses = $request->has('packageStatuses') ? $request->packageStatuses : [];
        $f_paymentStatuses = $request->has('paymentStatuses') ? $request->paymentStatuses : [];

        $obj = new User();
        $obj->name = $request->name;
        $obj->username = $request->username;
        $obj->password = bcrypt($request->password);
        $obj->guards = $request->guards ?: [];
        $obj->permissions = $request->permissions ?: [];
        $obj->api_permissions = array_map('intval', $request->api_permissions ?: []);
        $obj->queries = [
            'locations' => array_map('intval', $f_locations),
            'transportTypes' => array_map('intval', $f_transportTypes),
            'packagePayments' => array_map('intval', $f_packagePayments),
            'packageTypes' => array_map('intval', $f_packageTypes),
            'packageStatuses' => array_map('intval', $f_packageStatuses),
            'paymentStatuses' => array_map('intval', $f_paymentStatuses),
        ];
        $obj->save();
        $obj->tasks()->sync($request->tasks ?: []);

        return to_route('admin.users.index')
            ->with([
                'success' => trans('app.user') . ' ' . trans('app.added'),
            ]);
    }

    public function edit($id)
    {
        $obj = User::when(auth()->id() != 1, fn($query) => $query->where('id', '!=', 1))
            ->findOrFail($id);
        $tasks = Task::orderBy('name')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.user.edit')
            ->with([
                'obj' => $obj,
                'guards' => $this->getGuards(),
                'tasks' => $tasks,
                'permissions' => $this->getPermissions(),
                'api_permissions' => $this->getApiPermissions(),
                'queries' => [
                    'locations' => config('const.locations'),
                    'transportTypes' => config('const.transportTypes'),
                    'packagePayments' => config('const.packagePayments'),
                    'packageTypes' => config('const.packageTypes'),
                    'packageStatuses' => config('const.packageStatuses'),
                    'paymentStatuses' => config('const.paymentStatuses'),
                ],
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'max:50'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['nullable', 'integer', 'min:1'],
            'guards' => ['nullable', 'array'],
            'guards.*' => ['nullable', 'integer', 'min:1'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'integer', 'min:1'],
            'api_permissions' => ['nullable', 'array'],
            'api_permissions.*' => ['nullable', 'integer', 'min:1'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['nullable', 'integer', 'between:0,5'],
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'packagePayments' => ['nullable', 'array'],
            'packagePayments.*' => ['nullable', 'integer', 'between:0,2'],
            'packageTypes' => ['nullable', 'array'],
            'packageTypes.*' => ['nullable', 'integer', 'between:0,6'],
            'packageStatuses' => ['nullable', 'array'],
            'packageStatuses.*' => ['nullable', 'integer', 'between:0,5'],
            'paymentStatuses' => ['nullable', 'array'],
            'paymentStatuses.*' => ['nullable', 'integer', 'between:0,3'],
        ]);
        $f_locations = $request->has('locations') ? $request->locations : [];
        $f_transportTypes = $request->has('transportTypes') ? $request->transportTypes : [];
        $f_packagePayments = $request->has('packagePayments') ? $request->packagePayments : [];
        $f_packageTypes = $request->has('packageTypes') ? $request->packageTypes : [];
        $f_packageStatuses = $request->has('packageStatuses') ? $request->packageStatuses : [];
        $f_paymentStatuses = $request->has('paymentStatuses') ? $request->paymentStatuses : [];

        $obj = User::when(auth()->id() != 1, fn($query) => $query->where('id', '!=', 1))
            ->findOrFail($id);
        $obj->name = $request->name;
        $obj->username = $request->username;
        if (isset($request->password)) {
            $obj->password = bcrypt($request->password);
        }
        $obj->guards = $request->guards ?: [];
        $obj->permissions = $request->permissions ?: [];
        $obj->api_permissions = array_map('intval', $request->api_permissions ?: []);
        $obj->queries = [
            'locations' => array_map('intval', $f_locations),
            'transportTypes' => array_map('intval', $f_transportTypes),
            'packagePayments' => array_map('intval', $f_packagePayments),
            'packageTypes' => array_map('intval', $f_packageTypes),
            'packageStatuses' => array_map('intval', $f_packageStatuses),
            'paymentStatuses' => array_map('intval', $f_paymentStatuses),
        ];
        $obj->update();
        $obj->tasks()->sync($request->tasks ?: []);

        return to_route('admin.users.index')
            ->with([
                'success' => trans('app.user') . ' ' . trans('app.updated'),
            ]);
    }

    public function destroy($id)
    {
        $obj = User::when(auth()->id() != 1, fn($query) => $query->where('id', '!=', 1))
            ->findOrFail($id);
        if ($obj->id == 1) {
            return redirect()->back()
                ->with([
                    'error' => trans('app.error'),
                ]);
        }
        $obj->delete();

        return to_route('admin.users.index')
            ->with([
                'success' => trans('app.user') . ' ' . trans('app.deleted'),
            ]);
    }

    private function getGuards()
    {
        return [
            ['id' => 1, 'name' => trans('app.web')],
            ['id' => 2, 'name' => trans('app.api')],
        ];
    }

    private function getPermissions()
    {
        $permissions = [
            ['id' => 1, 'name' => trans('app.packagesPanel')],
            ['id' => 3, 'name' => trans('app.visitorsPanel')],
            ['id' => 4, 'name' => trans('app.adminPanel')],
            ['id' => 5, 'name' => trans('app.errors')],
            ['id' => 23, 'name' => trans('app.tokens')],
            ['id' => 6, 'name' => trans('app.packages')],
            ['id' => 7, 'name' => trans('app.transports')],
            ['id' => 8, 'name' => trans('app.customers')],
            ['id' => 9, 'name' => trans('app.verifications')],
            ['id' => 10, 'name' => trans('app.contacts')],
            ['id' => 24, 'name' => trans('app.banners')],
            ['id' => 11, 'name' => trans('app.notifications')],
            ['id' => 12, 'name' => trans('app.pushNotifications')],
            ['id' => 16, 'name' => trans('app.tasks')],
            ['id' => 17, 'name' => trans('app.users')],
            ['id' => 18, 'name' => trans('app.configs')],
            ['id' => 19, 'name' => trans('app.ipAddresses')],
            ['id' => 20, 'name' => trans('app.userAgents')],
            ['id' => 21, 'name' => trans('app.authAttempts')],
            ['id' => 22, 'name' => trans('app.visitors')],
        ];

        return auth()->id() != 1
            ? collect($permissions)->whereIn('id', [3, 6, 7, 8, 9, 10, 24, 11, 16, 17])->toArray()
            : $permissions;
    }

    private function getApiPermissions()
    {
        return [
            ['id' => 1, 'name' => trans('app.package') . ': ' . trans('app.search')],
            ['id' => 2, 'name' => trans('app.package') . ': ' . trans('app.add')],
            ['id' => 3, 'name' => trans('app.package') . ': ' . trans('app.status')],
            ['id' => 4, 'name' => trans('app.package') . ': ' . trans('app.payment')],
            ['id' => 5, 'name' => trans('app.transport') . ': ' . trans('app.search')],
            ['id' => 8, 'name' => trans('app.customer') . ': ' . trans('app.search')],
            ['id' => 9, 'name' => trans('app.customer') . ': ' . trans('app.add')],
        ];
    }
}
