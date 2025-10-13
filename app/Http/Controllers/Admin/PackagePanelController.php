<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackagePanelController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'transports' => ['nullable', 'array'],
            'transports.*' => ['nullable', 'integer', 'min:0'],
            'customers' => ['nullable', 'array'],
            'customers.*' => ['nullable', 'integer', 'min:0'],
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
        $f_transports = $request->transports ?: [];
        $f_customers = $request->customers ?: [];
        $f_locations = $request->locations ?: [];
        $f_transportTypes = $request->transportTypes ?: [];
        $f_packagePayments = $request->packagePayments ?: [];
        $f_packageTypes = $request->packageTypes ?: [];
        $f_packageStatuses = $request->packageStatuses ?: [];
        $f_paymentStatuses = $request->paymentStatuses ?: [];

        return view('admin.packagePanel.index')
            ->with([
                'queries' => [
                    'locations' => config('const.locations'),
                    'transportTypes' => config('const.transportTypes'),
                    'packagePayments' => config('const.packagePayments'),
                    'packageTypes' => config('const.packageTypes'),
                    'packageStatuses' => config('const.packageStatuses'),
                    'paymentStatuses' => config('const.paymentStatuses'),
                ],
                'f_transports' => $f_transports,
                'f_customers' => $f_customers,
                'f_locations' => $f_locations,
                'f_transportTypes' => $f_transportTypes,
                'f_packagePayments' => $f_packagePayments,
                'f_packageTypes' => $f_packageTypes,
                'f_packageStatuses' => $f_packageStatuses,
                'f_paymentStatuses' => $f_paymentStatuses,
            ]);
    }
}
