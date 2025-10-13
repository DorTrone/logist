<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Customer;
use App\Models\Error;
use App\Models\Package;
use App\Models\PaymentReport;
use App\Models\PushNotification;
use App\Models\Transport;
use Exception;
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
            'q' => ['nullable', 'string', 'max:100'],
            'perPage' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package index 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $f_transports = $request->has('transports') ? $request->transports : [];
        $f_customers = $request->has('customers') ? $request->customers : [];
        $f_locations = $request->has('locations') ? $request->locations : [];
        $f_transportTypes = $request->has('transportTypes') ? $request->transportTypes : [];
        $f_packagePayments = $request->has('packagePayments') ? $request->packagePayments : [];
        $f_packageTypes = $request->has('packageTypes') ? $request->packageTypes : [];
        $f_packageStatuses = $request->has('packageStatuses') ? $request->packageStatuses : [];
        $f_paymentStatuses = $request->has('paymentStatuses') ? $request->paymentStatuses : [];
        $q = ($request->has('q') and isset($request->q)) ? $request->q : null;
        $f_perPage = $request->has('perPage') ? $request->perPage : 20;
        $f_page = $request->has('page') ? $request->page : 1;

        $objs = Package::filterQuery('api', $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
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
            ->with('transport', 'customer', 'paymentReports', 'actions')
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
                    'note' => $obj->note,
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
                        'next' => $obj->nextStatuses(),
                        'next4' => $obj->next4Statuses(),
                    ],
                    'paymentStatus' => [
                        'id' => config('const.paymentStatuses')[$obj->payment_status]['id'],
                        'name' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], 'cn'),
                        'color' => config('const.paymentStatuses')[$obj->payment_status]['color'],
                        'next' => $obj->nextPaymentStatuses(),
                    ],
                    'transport' => [
                        'id' => $obj->transport->id,
                        'code' => $obj->transport->code,
                    ],
                    'customer' => [
                        'id' => $obj->customer->id,
                        'code' => $obj->customer->code,
                        'name' => $obj->customer->name,
                        'surname' => $obj->customer->surname,
                        'note' => $obj->customer->note,
                    ],
                    'paymentReports' => $obj->paymentReports
                        ->transform(function ($obj) {
                            return [
                                'id' => $obj->id,
                                'price' => round($obj->price_tmt, 0),
                                'note' => $obj->note,
                                'images' => $obj->getImages(true),
                                'createdAt' => $obj->created_at->format('d.m.Y H:i'),
                                'payment' => [
                                    'id' => config('const.payments')[$obj->payment]['id'],
                                    'name' => trans('const.' . config('const.payments')[$obj->payment]['name'], [], 'en'),
                                    'name_tm' => trans('const.' . config('const.payments')[$obj->payment]['name'], [], 'tm'),
                                    'name_ru' => trans('const.' . config('const.payments')[$obj->payment]['name'], [], 'ru'),
                                    'name_cn' => trans('const.' . config('const.payments')[$obj->payment]['name'], [], 'cn'),
                                ],
                            ];
                        }),
                    'actions' => $obj->actions
                        ->transform(function ($obj) {
                            return [
                                'id' => $obj->id,
                                'updatedBy' => $obj->updated_by,
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
                                'note' => $obj->note,
                                'images' => $obj->getImages(true),
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer' => ['required', 'integer', 'min:1'],
            'barcode' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string', 'max:50'],
            'location' => ['required', 'integer', 'between:0,5'],
            'packageType' => ['required', 'integer', 'between:0,6'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package store 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $transport = Transport::where('status', 0)
                ->orderBy('id', $request->packageType ? 'asc' : 'desc')
                ->firstOrFail();
            $customer = Customer::withTrashed()
                ->findOrFail($request->customer);
            $weightPrice = round(config('const.packageTypes')[$request->packageType]['price'] * config('const.usdToTmt'), 0);
            $transportType = config('const.packageTypes')[$request->packageType]['transportType'];
            $packagePayment = config('const.packageTypes')[$request->packageType]['packagePayment'];

            $obj = Package::create([
                'transport_id' => $transport->id,
                'customer_id' => $customer->id,
                'user_id' => auth('api')->id(),
                'code' => str()->random(5),
                'barcode' => $request->barcode,
                'weight' => round($request->weight, 2),
                'weight_price' => round($weightPrice, 0),
                'total_price' => round($request->weight * $weightPrice, 0),
                'note' => ($request->has('note') and isset($request->note)) ? $request->note : null,
                'images' => ($request->has('images') and isset($request->images)) ? $request->images : null,
                'location' => $request->location,
                'transport_type' => $transportType,
                'payment' => $packagePayment,
                'type' => $request->packageType,
                'status' => 0,
                'payment_status' => 0,
            ]);

            $obj->code = 'P' . $obj->id;
            $obj->ext_keyword = str('P' . $obj->id
                . ' T' . $obj->transport_id
                . ' ' . strval($obj->customer_id + 1000)
                . ' SZD' . strval($obj->customer_id + 1000)
                . ' ' . $obj->barcode
                . ' ' . substr($obj->barcode, -6))->squish()->lower()->slug(' ');
            $obj->update();

            $action = new Action();
            $action->package_id = $obj->id;
            $action->updated_by = auth('api')->user()->getName();
            $action->updates = ['packageStatus' => $obj->status, 'paymentStatus' => $obj->payment_status];
            $action->save();

            $pn = new PushNotification();
            $pn->push = 'app';
            $pn->to = 'shazada_app_' . $customer->id;
            $pn->title = trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], $customer->languageCode())
                . ', ' . trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], $customer->languageCode());
            $pn->body = $obj->getName();
            $pn->datetime = now()->addMinute()->startOfMinute();
            $pn->save();

        } catch (Exception $e) {
            Error::create([
                'title' => 'User Package store Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Package added',
        ], Response::HTTP_OK);
    }

    public function status(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'packageStatus' => ['required', 'integer', 'between:0,5'],
            'note' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string', 'max:50'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package status 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $obj = Package::with('customer')
                ->findOrFail($id);
            $obj->status = $request->packageStatus;
            $obj->update();

            $action = new Action();
            $action->package_id = $obj->id;
            $action->updated_by = auth('api')->user()->getName();
            $action->updates = ['packageStatus' => $obj->status];
            $action->note = ($request->has('note') and isset($request->note)) ? $request->note : null;
            $action->images = ($request->has('images') and isset($request->images)) ? $request->images : null;
            $action->save();

            $pn = new PushNotification();
            $pn->push = 'app';
            $pn->to = 'shazada_app_' . $obj->customer->id;
            $pn->title = trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], $obj->customer->languageCode());
            $pn->body = $obj->getName();
            $pn->datetime = now()->addMinute()->startOfMinute();
            $pn->save();

        } catch (Exception $e) {
            Error::create([
                'title' => 'User Package status Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Package updated',
        ], Response::HTTP_OK);
    }

    public function payment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'paymentStatus' => ['required', 'integer', 'between:1,2'],
            'paymentMethod' => ['required', 'integer', 'between:0,2'], // Cash, WeChat Pay, Alipay
            'price' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string', 'max:50'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package payment 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $obj = Package::with('customer')
                ->findOrFail($id);
            $obj->payment_status = $request->paymentStatus;
            $obj->update();

            $action = new Action();
            $action->package_id = $obj->id;
            $action->updated_by = auth('api')->user()->getName();
            $action->updates = ['paymentStatus' => $obj->payment_status];
            $action->note = ($request->has('note') and isset($request->note)) ? $request->note : null;
            $action->images = ($request->has('images') and isset($request->images)) ? $request->images : null;
            $action->save();

            $paymentReport = new PaymentReport();
            $paymentReport->package_id = $obj->id;
            $paymentReport->paid_by = auth('api')->user()->getName();
            $paymentReport->payment = $request->paymentMethod;
            $paymentReport->price_tmt = round($request->price, 0);
            $paymentReport->note = ($request->has('note') and isset($request->note)) ? $request->note : null;
            $paymentReport->images = ($request->has('images') and isset($request->images)) ? $request->images : null;
            $paymentReport->save();

            $pn = new PushNotification();
            $pn->push = 'app';
            $pn->to = 'shazada_app_' . $obj->customer->id;
            $pn->title = trans('const.' . config('const.paymentStatuses')[$obj->payment_status]['name'], [], $obj->customer->languageCode());
            $pn->body = $obj->getName();
            $pn->datetime = now()->addMinute()->startOfMinute();
            $pn->save();

        } catch (Exception $e) {
            Error::create([
                'title' => 'User Package payment Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Package updated',
        ], Response::HTTP_OK);
    }

    public function image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package image 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $requestImage = $request->file('image');
            $imageName = str()->random(10) . '.' . 'webp';

            $manager = new ImageManager(new Driver());
            $uploadImage = $manager->read($requestImage)->scale(1500, 1500)->toWebp(85)->toString();
            $image = $manager->read(public_path('img/1500x1500.png'))->place($uploadImage, 'center')->toWebp(85)->toString();
            $imageUrl = 'p/' . $imageName;
            Storage::disk('public')->put($imageUrl, $image);

        } catch (Exception $e) {
            Error::create([
                'title' => 'User Package image Exception',
                'body' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 1,
            'data' => 'p/' . $imageName,
        ], Response::HTTP_OK);
    }

    public function quick(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => ['required', 'string', 'max:255'],
            'packageStatus' => ['required', 'integer', 'between:0,5'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Package quick 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = Package::where('barcode', $request->barcode)
            ->with('customer')
            ->first();
        if (!$obj) {
            return response()->json([
                'status' => 0,
                'message' => 'Package not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $obj->status = $request->packageStatus;
        $obj->update();

        $action = new Action();
        $action->package_id = $obj->id;
        $action->updated_by = auth('api')->user()->getName();
        $action->updates = ['packageStatus' => $obj->status];
        $action->save();

        $pn = new PushNotification();
        $pn->push = 'app';
        $pn->to = 'shazada_app_' . $obj->customer->id;
        $pn->title = trans('const.' . config('const.packageStatuses')[$obj->status]['name'], [], $obj->customer->languageCode());
        $pn->body = $obj->getName();
        $pn->datetime = now()->addMinute()->startOfMinute();
        $pn->save();

        return response()->json([
            'status' => 1,
            'message' => 'Package updated',
        ], Response::HTTP_OK);
    }
}
