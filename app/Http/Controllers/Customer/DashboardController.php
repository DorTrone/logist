<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Package;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $banners = Banner::whereNotNull('image')
            ->where('datetime_start', '<=', now())
            ->where('datetime_end', '>=', now())
            ->orderBy('sort_order')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'image' => $obj->getImage(null, true),
                    'imageTm' => $obj->getImage('tm', true),
                    'imageRu' => $obj->getImage('ru', true),
                    'imageCn' => $obj->getImage('cn', true),
                    'image2' => $obj->getImage2(null, true),
                    'image2Tm' => $obj->getImage2('tm', true),
                    'image2Ru' => $obj->getImage2('ru', true),
                    'image2Cn' => $obj->getImage2('cn', true),
                    'url' => $obj->url,
                ];
            });

        $packages = Package::where('customer_id', auth('customer_api')->id())
            ->selectRaw('count(id) as count, transport_type, status')
            ->groupBy('transport_type', 'status')
            ->orderBy('transport_type')
            ->orderBy('status')
            ->get();

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
            'packageStatuses' => collect(config('const.packageStatuses'))
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
                'banners' => $banners,
                'transports' => collect(config('const.transportTypes'))
                    ->transform(function ($obj) use ($packages) {
                        return [
                            'transportType' => [
                                'id' => $obj['id'],
                                'name' => trans('const.' . $obj['name'], [], 'en'),
                                'name_tm' => trans('const.' . $obj['name'], [], 'tm'),
                                'name_ru' => trans('const.' . $obj['name'], [], 'ru'),
                                'name_cn' => trans('const.' . $obj['name'], [], 'cn'),
                            ],
                            'packages' => $packages->where('transport_type', $obj['id'])
                                ->transform(function ($obj) {
                                    return [
                                        'packageStatus' => [
                                            'id' => config('const.packageStatuses')[$obj->status]['id'],
                                            'name' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'en'),
                                            'name_tm' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'tm'),
                                            'name_ru' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'ru'),
                                            'name_cn' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'cn'),
                                            'color' => config('const.packageStatuses')[$obj->status]['color'],
                                        ],
                                        'count' => $obj->count,
                                    ];
                                }),
                        ];
                    }),
                'attributes' => $attributes,
            ],
        ], Response::HTTP_OK);
    }
}
