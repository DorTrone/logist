<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:100'],
            'perPage' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Customer index 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $q = ($request->has('q') and isset($request->q)) ? $request->q : null;
        $f_perPage = $request->has('perPage') ? $request->perPage : 20;
        $f_page = $request->has('page') ? $request->page : 1;

        $objs = Customer::withTrashed()
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
            ->withCount([
                'packages as unpaid_packages_count' => function ($query) {
                    $query->whereIn('payment_status', [0, 1, 3]);
                },
                'packages as undelivered_packages_count' => function ($query) {
                    $query->whereIn('status', [0, 1, 2, 3, 4]);
                },
                'packages as delivered_packages_count' => function ($query) {
                    $query->where('status', 5);
                },
            ])
            ->orderBy('id', 'desc')
            ->paginate($f_perPage, ['*'], 'page', $f_page)
            ->withQueryString();

        $objs->getCollection()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'code' => $obj->code,
                    'name' => $obj->name,
                    'surname' => $obj->surname,
                    'username' => $obj->username,
                    'note' => $obj->note,
                    'unpaidPackages' => [
                        'count' => $obj->unpaid_packages_count,
                        'queries' => ['paymentStatuses' => [0, 1, 3]],
                    ],
                    'undeliveredPackages' => [
                        'count' => $obj->undelivered_packages_count,
                        'queries' => ['packageStatuses' => [0, 1, 2, 3, 4]],
                    ],
                    'deliveredPackages' => [
                        'count' => $obj->delivered_packages_count,
                        'queries' => ['packageStatuses' => [5]],
                    ],
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
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'alpha_num', 'max:50', Rule::unique('customers')],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Customer store 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer = Customer::create([
            'code' => str()->random(5),
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'note' => $request->note ?: null,
            'auth_method' => 2,
        ]);

        $customer->code = 'SZD' . strval($customer->id + 1000);
        $customer->ext_keyword = str(strval($customer->id + 1000)
            . ' SZD' . strval($customer->id + 1000)
            . ' ' . $customer->name
            . ' ' . $customer->surname
            . ' ' . $customer->username)->squish()->lower()->slug(' ');
        $customer->update();

        return response()->json([
            'status' => 1,
            'message' => 'Customer added',
        ], Response::HTTP_OK);
    }
}
