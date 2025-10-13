<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $objs = Task::orderBy('name')
            ->orderBy('id', 'desc')
            ->with('users')
            ->get();

        return view('admin.task.index')
            ->with([
                'objs' => $objs,
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
        return view('admin.task.create')
            ->with([
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
            'name_tm' => ['nullable', 'string', 'max:50'],
            'name_ru' => ['nullable', 'string', 'max:50'],
            'name_cn' => ['nullable', 'string', 'max:50'],
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

        $obj = new Task();
        $obj->name = $request->name;
        $obj->name_tm = $request->name_tm ?: null;
        $obj->name_ru = $request->name_ru ?: null;
        $obj->name_cn = $request->name_cn ?: null;
        $obj->queries = [
            'locations' => array_map('intval', $f_locations),
            'transportTypes' => array_map('intval', $f_transportTypes),
            'packagePayments' => array_map('intval', $f_packagePayments),
            'packageTypes' => array_map('intval', $f_packageTypes),
            'packageStatuses' => array_map('intval', $f_packageStatuses),
            'paymentStatuses' => array_map('intval', $f_paymentStatuses),
        ];
        $obj->save();

        return to_route('admin.tasks.index')
            ->with([
                'success' => trans('app.task') . ' ' . trans('app.added'),
            ]);
    }

    public function edit($id)
    {
        $obj = Task::findOrFail($id);

        return view('admin.task.edit')
            ->with([
                'obj' => $obj,
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
            'name_tm' => ['nullable', 'string', 'max:50'],
            'name_ru' => ['nullable', 'string', 'max:50'],
            'name_cn' => ['nullable', 'string', 'max:50'],
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

        $obj = Task::findOrFail($id);
        $obj->name = $request->name;
        $obj->name_tm = $request->name_tm ?: null;
        $obj->name_ru = $request->name_ru ?: null;
        $obj->name_cn = $request->name_cn ?: null;
        $obj->queries = [
            'locations' => array_map('intval', $f_locations),
            'transportTypes' => array_map('intval', $f_transportTypes),
            'packagePayments' => array_map('intval', $f_packagePayments),
            'packageTypes' => array_map('intval', $f_packageTypes),
            'packageStatuses' => array_map('intval', $f_packageStatuses),
            'paymentStatuses' => array_map('intval', $f_paymentStatuses),
        ];
        $obj->update();

        return to_route('admin.tasks.index')
            ->with([
                'success' => trans('app.task') . ' ' . trans('app.updated'),
            ]);
    }

    public function destroy($id)
    {
        $obj = Task::findOrFail($id);
        $obj->delete();

        return to_route('admin.tasks.index')
            ->with([
                'success' => trans('app.task') . ' ' . trans('app.deleted'),
            ]);
    }
}
