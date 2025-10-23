<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Error;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'packageStatuses' => ['nullable', 'array'],
            'packageStatuses.*' => ['nullable', 'integer', 'between:0,5'],
            'paymentStatuses' => ['nullable', 'array'],
            'paymentStatuses.*' => ['nullable', 'integer', 'between:0,3'],
            'q' => ['nullable', 'string', 'max:100'],
            'perPage' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Package index 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $q = ($request->has('q') and isset($request->q)) ? $request->q : null;
        $f_transportTypes = $request->has('transportTypes') ? $request->transportTypes : [];
        $f_packageStatuses = $request->has('packageStatuses') ? $request->packageStatuses : [];
        $f_paymentStatuses = $request->has('paymentStatuses') ? $request->paymentStatuses : [];
        $f_perPage = $request->has('perPage') ? $request->perPage : 20;
        $f_page = $request->has('page') ? $request->page : 1;

        $objs = Package::filterQuery('customer_api', [], [auth('customer_api')->id()], [], $f_transportTypes, [], [], $f_packageStatuses, $f_paymentStatuses)
            ->when(isset($q), function ($query) use ($q) {
                $search = $q;
                $searchTerm = str($search)->squish()->lower()->slug(' ');
                $words = preg_split('/\s+/', $searchTerm);
                $filteredWords = array_filter(array_map(function ($word) {
                    return preg_replace('/[^a-zA-Z0-9]/', '', $word);
                }, $words));
                $searchQuery = implode(' & ', array_map(function ($word) {
                    return $word . ':*';
                }, $filteredWords));
                return $query->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery]);
            })
            ->orderBy('id', 'desc')
            ->with('actions')
            ->paginate($f_perPage, ['*'], 'page', $f_page)
            ->withQueryString();

        $objs->getCollection()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'code' => $obj->code,
                    'barcode' => $obj->barcode,
                    'weight' => round($obj->weight, 2),
                    'weightPrice' => round($obj->weight_price, 0),
                    'totalPrice' => round($obj->total_price, 0),
                    'images' => $obj->getImages(true),
                    'location' => [
                        'id' => config('const.locations')[$obj->location]['id'],
                        'name' => trans('const.' . config('const.locations')[$obj->location]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.locations')[$obj->location]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.locations')[$obj->location]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.locations')[$obj->location]['name'], [], 'cn'),
                    ],
                    'transportType' => [
                        'id' => config('const.transportTypes')[$obj->transport_type]['id'],
                        'name' => trans('const.' . config('const.transportTypes')[$obj->transport_type]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.transportTypes')[$obj->transport_type]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.transportTypes')[$obj->transport_type]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.transportTypes')[$obj->transport_type]['name'], [], 'cn'),
                    ],
                    'packagePayment' => [
                        'id' => config('const.packagePayments')[$obj->payment]['id'],
                        'name' => trans('const.' . config('const.packagePayments')[$obj->payment]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.packagePayments')[$obj->payment]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.packagePayments')[$obj->payment]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.packagePayments')[$obj->payment]['name'], [], 'cn'),
                        'ext' => trans('const.' . config('const.packagePayments')[$obj->payment]['ext'], [], 'en'),
                        'ext_tm' => trans('const.' . config('const.packagePayments')[$obj->payment]['ext'], [], 'tm'),
                        'ext_ru' => trans('const.' . config('const.packagePayments')[$obj->payment]['ext'], [], 'ru'),
                        'ext_cn' => trans('const.' . config('const.packagePayments')[$obj->payment]['ext'], [], 'cn'),
                    ],
                    'packageType' => [
                        'id' => config('const.packageTypes')[$obj->type]['id'],
                        'name' => trans('const.' . config('const.packageTypes')[$obj->type]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.packageTypes')[$obj->type]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.packageTypes')[$obj->type]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.packageTypes')[$obj->type]['name'], [], 'cn'),
                    ],
                    'packageStatus' => [
                        'id' => config('const.packageStatuses')[$obj->status]['id'],
                        'name' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], 'cn'),
                        'color' => config('const.packageStatuses')[$obj->status]['color'],
                        'next4' => $obj->next4Statuses(),
                    ],
                    'paymentStatus' => [
                        'id' => config('const.paymentStatuses')[$obj->payment_status]['id'],
                        'name' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'cn'),
                        'color' => config('const.paymentStatuses')[$obj->payment_status]['color'],
                    ],
                    'actions' => $obj->actions
                        ->transform(function ($obj) {
                            return [
                                'id' => $obj->id,
                                'updates' => collect($obj->updates)
                                    ->transform(function ($i, $k) {
                                        return [
                                            'id' => config('const.' . str($k)->plural())[$i]['id'],
                                            'name' => trans('const.' . config('const.' . str($k)->plural())[$i]['name'], [], 'en'),
                                            'name_tm' => trans('const.' . config('const.' . str($k)->plural())[$i]['name'], [], 'tm'),
                                            'name_ru' => trans('const.' . config('const.' . str($k)->plural())[$i]['name'], [], 'ru'),
                                            'name_cn' => trans('const.' . config('const.' . str($k)->plural())[$i]['name'], [], 'cn'),
                                        ];
                                    }),
                                'createdAt' => $obj->created_at->format('d.m.Y H:i'),
                            ];
                        }),
                ];
            });
        $objs = collect($objs)
            ->forget(['current_page', 'first_page_url', 'from', 'last_page_url', 'links', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to'])
            ->toArray();

        return response()->json(
            array_merge(['status' => 1], $objs),
            Response::HTTP_OK);
    }


    public function payment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Package payment 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = Package::where('customer_id', auth('customer_api')->id())
            ->findOrFail($id);
        $obj->payment_status = 3;
        $obj->update();

        $requestImage = $request->file('image');
        $imageName = str()->random(10) . '.' . 'webp';

        $manager = new ImageManager(new Driver());
        $uploadImage = $manager->read($requestImage)->scale(1500, 1500)->toWebp(85)->toString();
        $image = $manager->read(public_path('img/1500x1500.png'))->place($uploadImage, 'center')->toWebp(85)->toString();
        $imageUrl = 'c/' . $imageName;
        Storage::disk('public')->put($imageUrl, $image);

        $action = new Action();
        $action->package_id = $obj->id;
        $action->updated_by = auth('customer_api')->user()->getName();
        $action->updates = ['paymentStatus' => $obj->payment_status];
        $action->note = ($request->has('note') and isset($request->note)) ? $request->note : null;
        $action->images = [$imageUrl];
        $action->save();

        return response()->json([
            'status' => 1,
            'message' => 'Payment status updated',
        ], Response::HTTP_OK);
    }
}
