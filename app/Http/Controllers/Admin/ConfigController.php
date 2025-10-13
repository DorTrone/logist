<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    public function edit()
    {
        $obj = Config::findOrFail(1);

        return view('admin.config.edit')
            ->with([
                'obj' => $obj,
            ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'android_version' => ['required', 'string', 'max:10'],
            'ios_version' => ['required', 'string', 'max:10'],
            'szd_customer' => ['nullable', 'file', 'max:65536'],
            'szd_employee' => ['nullable', 'file', 'max:65536'],
        ]);

        $obj = Config::findOrFail(1);
        $obj->android_version = $request->android_version;
        $obj->ios_version = $request->ios_version;
        $obj->update();

        if ($request->hasfile('szd_customer')) {
            Storage::disk('public')->delete('szd_customer.apk');
            Storage::disk('public')->putFileAs('', $request->file('szd_customer'), 'szd_customer.apk');
        }

        if ($request->hasfile('szd_employee')) {
            Storage::disk('public')->delete('szd_employee.apk');
            Storage::disk('public')->putFileAs('', $request->file('szd_employee'), 'szd_employee.apk');
        }

        return to_route('admin.configs.edit')
            ->with([
                'success' => trans('app.config') . ' ' . trans('app.updated'),
            ]);
    }
}
