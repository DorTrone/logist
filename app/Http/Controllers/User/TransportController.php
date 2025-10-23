<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Error;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transportTypes' => ['nullable', 'array'],
            'transportTypes.*' => ['nullable', 'integer', 'between:0,2'],
            'transportStatuses' => ['nullable', 'array'],
            'transportStatuses.*' => ['nullable', 'integer', 'between:0,1'],
            'q' => ['nullable', 'string', 'max:100'],
            'perPage' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Transport index 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $f_transportTypes = $request->has('transportTypes') ? $request->transportTypes : [];
        $f_transportStatuses = $request->has('transportStatuses') ? $request->transportStatuses : [];
        $q = ($request->has('q') and isset($request->q)) ? $request->q : null;
        $f_perPage = $request->has('perPage') ? $request->perPage : 20;
        $f_page = $request->has('page') ? $request->page : 1;

        $objs = Transport::filterQuery($f_transportTypes, $f_transportStatuses)
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
            ->with('actions')
            ->orderBy('id', 'desc')
            ->paginate($f_perPage, ['*'], 'page', $f_page)
            ->withQueryString();

        $objs->getCollection()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'code' => $obj->code,
                    'note' => $obj->note,
                    'images' => $obj->getImages(true),
                    'transportType' => [
                        'id' => config('const.transportTypes')[$obj->type]['id'],
                        'name' => trans('const.' . config('const.transportTypes')[$obj->type]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.transportTypes')[$obj->type]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.transportTypes')[$obj->type]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.transportTypes')[$obj->type]['name'], [], 'cn'),
                    ],
                    'transportStatus' => [
                        'id' => config('const.transportStatuses')[$obj->status]['id'],
                        'name' => trans('const.' . config('const.transportStatuses')[$obj->status]['name'], [], 'en'),
                        'name_tm' => trans('const.' . config('const.transportStatuses')[$obj->status]['name'], [], 'tm'),
                        'name_ru' => trans('const.' . config('const.transportStatuses')[$obj->status]['name'], [], 'ru'),
                        'name_cn' => trans('const.' . config('const.transportStatuses')[$obj->status]['name'], [], 'cn'),
                        'color' => config('const.transportStatuses')[$obj->status]['color'],
                        'next' => $obj->nextStatuses(),
                    ],
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
}
