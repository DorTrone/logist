<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Transport;
use Illuminate\Http\Request;

class PackageController extends Controller
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

        return view('admin.package.index')
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

    public function edit($id)
    {
        $obj = Package::findOrFail($id);

        return view('admin.package.edit')
            ->with([
                'queries' => [
                    'locations' => config('const.locations'),
                    'packageTypes' => config('const.packageTypes'),
                ],
                'obj' => $obj,
            ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer' => ['required', 'integer', 'min:1001'],
            'barcode' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0'],
            'location' => ['required', 'integer', 'between:0,5'],
            'packageType' => ['required', 'integer', 'between:0,6'],
        ]);

        $customer = Customer::withTrashed()
            ->findOrFail($request->customer - 1000);
        $weightPrice = round(config('const.packageTypes')[$request->packageType]['price'] * config('const.usdToTmt'), 0);
        $packagePayment = config('const.packageTypes')[$request->packageType]['packagePayment'];

        $obj = Package::findOrFail($id);
        $obj->customer_id = $customer->id;
        $obj->barcode = $request->barcode;
        $obj->weight = round($request->weight, 2);
        $obj->weight_price = round($weightPrice, 0);
        $obj->total_price = round($request->weight * $weightPrice, 0);
        $obj->location = $request->location;
        $obj->payment = $packagePayment;
        $obj->type = $request->packageType;

        $obj->ext_keyword = str('P' . $obj->id
            . ' T' . $obj->transport_id
            . ' ' . strval($obj->customer_id + 1000)
            . ' SZD' . strval($obj->customer_id + 1000)
            . ' ' . $obj->barcode
            . ' ' . substr($obj->barcode, -6))->squish()->lower()->slug(' ');
        $obj->update();

        $action = new Action();
        $action->package_id = $obj->id;
        $action->updated_by = auth()->user()->getName();
        $action->updates = ['packageStatus' => $obj->status, 'paymentStatus' => $obj->payment_status];
        $action->note = 'The package has been updated';
        $action->save();

        return to_route('admin.packages.index')
            ->with([
                'success' => trans('app.package') . ' ' . trans('app.updated'),
            ]);
    }

    public function api(Request $request)
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

        $columns = [
            'id',
            'code',
            'images',
            'note',
            'customer',
            'transport',
            'weight',
            'total_price',
            'status',
            'payment_reports',
            'actions',
        ];
        $total = Package::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Package::filterQuery('web', $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('transport', 'customer', 'paymentReports', 'actions')
                ->get();
            $totalFiltered = Package::filterQuery('web', $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
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
            $rs = Package::filterQuery('web', $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('transport', 'customer', 'paymentReports', 'actions')
                ->get();
            $totalFiltered = Package::filterQuery('web', $f_transports, $f_customers, $f_locations, $f_transportTypes, $f_packagePayments, $f_packageTypes, $f_packageStatuses, $f_paymentStatuses)
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['code'] = '<div class="font-monospace text-danger fw-semibold">' . $r->getName() . '</div>'
                    . '<div class="small font-monospace text-secondary">' . $r->barcode . '</div>';
                $nestedData['images'] = count($r->getImages(false)) > 0
                    ? ('<a data-fancybox="gallery" href="' . $r->getImages(false)[0] . '" data-caption="' . $r->getName() . '">'
                        . '<img src="' . $r->getImages(false)[0] . '" alt="' . $r->getName() . '" class="img-fluid">'
                        . '</a>'
                        . (collect($r->getImages(false))
                            ->transform(function ($obj, $i) {
                                return '<a data-fancybox="gallery" href="' . $obj . '" data-caption="#' . $i . '"><i class="bi-' . $i + 1 . '-square"></i></a>';
                            })->implode(' ')))
                    : '';
                $nestedData['note'] = isset($r->note) ? ('<div class="small text-danger">' . $r->note . '</div>') : '';
                $nestedData['customer'] = '<div class="font-monospace text-danger fw-semibold">' . $r->customer->code . '</div>'
                    . '<div class="fw-semibold"><span class="text-primary">' . $r->location() . '</span> ' . $r->customer->name . ' ' . $r->customer->surname . '</div>';;
                $nestedData['transport'] = '<div class="font-monospace text-danger fw-semibold">' . $r->transport->getName() . '</div>'
                    . '<div class="fw-semibold">' . $r->transport->type() . '</div>'
                    . '<div class="badge bg-' . $r->transport->statusColor() . '-subtle text-' . $r->transport->statusColor() . '-emphasis">' . $r->transport->status() . '</div>';
                $nestedData['weight'] = $r->weight_price > 0 ? ('<div class="fs-5">' . round($r->weight, 2) . ' <small>' . $r->paymentExt() . '</small></div>'
                    . '<div class="text-secondary">' . round($r->weight_price, 0) . ' <small>TMT/' . $r->paymentExt() . '</small></div>') : '';
                $nestedData['total_price'] = '<div class="fs-5 text-danger">' . round($r->total_price, 0) . ' <small>TMT</small></div>';
                $nestedData['status'] = '<div class="fw-semibold">' . $r->type() . '</div>'
                    . '<div class="fw-semibold">' . $r->transportType() . '</div>'
                    . '<div class="badge bg-' . $r->statusColor() . '-subtle text-' . $r->statusColor() . '-emphasis">' . $r->status() . '</div>';
                $paymentReports = [];
                foreach ($r->paymentReports as $paymentReport) {
                    $paymentReports[] = '<div class="small font-monospace">' . $paymentReport->created_at->format('Y-m-d H:i:s') . '</div>'
                        . '<div class="small"><span class="text-secondary">' . $paymentReport->payment() . ':</span> ' . round($paymentReport->price_tmt, 0) . ' <small>TMT</small>' . '</div>'
                        . (isset($paymentReport->note) ? ('<div class="small">' . $paymentReport->note . '</div>') : '')
                        . (collect($paymentReport->getImages(false))
                            ->transform(function ($obj, $i) {
                                return '<a data-fancybox="gallery" href="' . $obj . '" data-caption="#' . $i . '"><i class="bi-' . $i + 1 . '-square"></i></a>';
                            })->implode(' '))
                        . '<hr class="my-1">';
                }
                $nestedData['payment_reports'] = '<div class="fw-semibold">' . $r->payment() . '</div>'
                    . '<div class="badge bg-' . $r->paymentStatusColor() . '-subtle text-' . $r->paymentStatusColor() . '-emphasis">' . $r->paymentStatus() . '</div>'
                    . '<div class="my-1"><button class="btn btn-light btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $r->id . 'pr" aria-expanded="false" aria-controls="collapse' . $r->id . 'pr">' . trans('app.paymentReports') . '</button></div>'
                    . '<div id="collapse' . $r->id . 'pr" class="collapse small">' . implode('', $paymentReports ?: []) . '</div>';
                $actions = [];
                foreach ($r->actions as $action) {
                    $actions[] = '<div class="small font-monospace">' . $action->created_at->format('Y-m-d H:i:s') . '</div>'
                        . '<div class="small"><span class="text-secondary">' . $action->updated_by . ':</span> '
                        . (collect($action->updates)
                            ->transform(function ($i, $k) {
                                return trans('app.' . $k) . ': ' . trans('const.' . config('const.' . str($k)->plural())[$i]['name']);
                            })
                            ->implode(', '))
                        . '</div>'
                        . (isset($action->note) ? ('<div class="small">' . $action->note . '</div>') : '')
                        . (collect($action->getImages(false))
                            ->transform(function ($obj, $i) {
                                return '<a data-fancybox="gallery" href="' . $obj . '" data-caption="#' . $i . '"><i class="bi-' . $i + 1 . '-square"></i></a>';
                            })->implode(' '))
                        . '<hr class="my-1">';
                }
                $nestedData['actions'] = '<div class="small text-secondary">' . trans('app.createdAt') . ': ' . $r->created_at->format('Y-m-d H:i:s') . '</div>'
                    . '<div class="small text-secondary">' . trans('app.updatedAt') . ': ' . $r->updated_at->format('Y-m-d H:i:s') . '</div>'
                    . '<div class="my-1"><button class="btn btn-light btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $r->id . 'a" aria-expanded="false" aria-controls="collapse' . $r->id . 'a">' . trans('app.actions') . '</button></div>'
                    . '<div id="collapse' . $r->id . 'a" class="collapse small">' . implode('', $actions ?: []) . '</div>';
                $nestedData['action'] = '<a href="' . route('admin.packages.edit', $r->id) . '" class="btn btn-success btn-sm mb-1"><i class="bi-pencil"></i></a>';
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
