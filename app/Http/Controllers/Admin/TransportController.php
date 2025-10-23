<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'transportStatuses' => ['nullable', 'array'],
            'transportStatuses.*' => ['nullable', 'integer', 'between:0,1'],
        ]);
        $f_transportTypes = $request->transportTypes ?: [];
        $f_transportStatuses = $request->transportStatuses ?: [];

        return view('admin.transport.index')
            ->with([
                'queries' => [
                    'transportTypes' => config('const.transportTypes'),
                    'transportStatuses' => config('const.transportStatuses'),
                ],
                'f_transportTypes' => $f_transportTypes,
                'f_transportStatuses' => $f_transportStatuses,
            ]);
    }

    public function api(Request $request)
    {
        $request->validate([
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'transportStatuses' => ['nullable', 'array'],
            'transportStatuses.*' => ['nullable', 'integer', 'between:0,1'],
        ]);
        $f_transportTypes = $request->transportTypes ?: [];
        $f_transportStatuses = $request->transportStatuses ?: [];

        $columns = [
            'id',
            'code',
            'images',
            'note',
            'total_weight',
            'total_price',
            'status',
            'payment_reports',
            'actions',
            'packages_count',
        ];
        $total = Transport::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (!$request->input('search.value')) {
            $rs = Transport::filterQuery($f_transportTypes, $f_transportStatuses)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('paymentReports', 'actions')
                ->withCount('packages')
                ->get();
            $totalFiltered = Transport::filterQuery($f_transportTypes, $f_transportStatuses)
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
            $rs = Transport::filterQuery($f_transportTypes, $f_transportStatuses)
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->orderBy('id', 'desc')
                ->with('paymentReports', 'actions')
                ->withCount('packages')
                ->get();
            $totalFiltered = Transport::filterQuery($f_transportTypes, $f_transportStatuses)
                ->whereRaw("to_tsvector('simple', ext_keyword) @@ to_tsquery('simple', ?)", [$searchQuery])
                ->count();
        }
        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['code'] = '<div class="font-monospace text-danger fw-semibold">' . $r->getName() . '</div>';
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
                $nestedData['total_weight'] = '<div class="fs-5">' . round($r->total_weight, 2) . ' <small>Unit</small></div>';
                $nestedData['total_price'] = '<div class="fs-5 text-danger">' . round($r->total_price, 0) . ' <small>TMT</small></div>';
                $nestedData['status'] = '<div class="fw-semibold">' . $r->type() . '</div>'
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
                $nestedData['payment_reports'] = '<div class="mb-1"><button class="btn btn-light btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $r->id . 'pr" aria-expanded="false" aria-controls="collapse' . $r->id . 'pr">' . trans('app.paymentReports') . '</button></div>'
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
                $nestedData['packages_count'] = '<a href="' . route('admin.packages.index', ['transports' => [$r->id]]) . '" class="fs-5 text-decoration-none ' . ($r->packages_count > 0 ? '' : 'd-none') . '">' . $r->packages_count . ' <i class="bi-box-arrow-up-right"></i></a>';
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
