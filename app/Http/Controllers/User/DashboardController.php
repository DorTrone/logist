<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Task;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::whereHas('users', function ($query) {
            $query->where('id', auth('api')->id());
        })
            ->orderBy('id', 'desc')
            ->get();

        $objs = [];

        foreach ($tasks as $task) {
            $objs[] = [
                'name' => $task->name,
                'name_tm' => $task->name_tm,
                'name_ru' => $task->name_ru,
                'name_cn' => $task->name_cn,
                'count' => intval(Package::filterQuery(
                    'api',
                    [],
                    [],
                    $task->queries['locations'],
                    $task->queries['transportTypes'],
                    $task->queries['packagePayments'],
                    $task->queries['packageTypes'],
                    $task->queries['packageStatuses'],
                    $task->queries['paymentStatuses'],
                )->count()),
                'queries' => $task->queries,
            ];
        }

        $objs = collect($objs)->sortByDesc('count')->values();

        $attributes = [
            'transportTypes' => collect(config('const.transportTypes'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                    ];
                }),
            'transportStatuses' => collect(config('const.transportStatuses'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                        'color' => $obj['color'],
                    ];
                }),
            'locations' => collect(config('const.locations'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                    ];
                }),
            'packagePayments' => collect(config('const.packagePayments'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                        'ext' => trans('const.' . $obj['ext'], [], 'en'),
                        'ext_tm' => trans('const.' . $obj['ext'], [], 'tm'),
                        'ext_ru' => trans('const.' . $obj['ext'], [], 'ru'),
                        'ext_cn' => trans('const.' . $obj['ext'], [], 'cn'),
                    ];
                }),
            'packageTypes' => collect(config('const.packageTypes'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                        'transportType' => $obj['transportType'],
                        'packagePayment' => $obj['packagePayment'],
                    ];
                }),
            'packageStatuses' => collect(config('const.packageStatuses'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                        'color' => $obj['color'],
                        'auto' => $obj['auto'],
                    ];
                }),
            'paymentStatuses' => collect(config('const.paymentStatuses'))
                ->transform(function ($obj) {
                    return [
                        'id' => $obj['id'],
                        'name' => trans('const.' . $obj['name'], [], 'en'),
                        'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                        'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                        'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                        'color' => $obj['color'],
                    ];
                }),
        ];

        return response()->json([
            'status' => 1,
            'data' => [
                'tasks' => $objs,
                'attributes' => $attributes,
                'permissions' => auth('api')->user()->api_permissions,
            ],
        ], Response::HTTP_OK);
    }
}
