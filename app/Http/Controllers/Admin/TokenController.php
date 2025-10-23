<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tokenType' => ['required', 'integer', 'between:0,1'],
            'tokenId' => ['required', 'integer', 'min:1'],
        ]);
        $f_tokenType = $request->tokenType;
        $f_tokenId = $request->tokenId;

        return view('admin.token.index')
            ->with([
                'f_tokenType' => $f_tokenType,
                'f_tokenId' => $f_tokenId,
            ]);
    }

    public function api(Request $request)
    {
        $request->validate([
            'tokenType' => ['required', 'integer', 'between:0,1'],
            'tokenId' => ['required', 'integer', 'min:1'],
        ]);
        $f_tokenType = $request->tokenType;
        $f_tokenId = $request->tokenId;

        $columns = [
            'id',
            'tokenable_type',
            'tokenable_id',
            'name',
            'last_used_at',
            'expires_at',
            'created_at',
            'updated_at',
        ];
        $total = PersonalAccessToken::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $rs = PersonalAccessToken::where('tokenable_type', ['App\Models\User', 'App\Models\Customer'][$f_tokenType])
            ->where('tokenable_id', $f_tokenId)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->orderBy('id', 'desc')
            ->get();
        $totalFiltered = PersonalAccessToken::where('tokenable_type', ['App\Models\User', 'App\Models\Customer'][$f_tokenType])
            ->where('tokenable_id', $f_tokenId)
            ->count();

        $data = [];
        if ($rs) {
            foreach ($rs as $r) {
                $nestedData['id'] = $r->id;
                $nestedData['tokenable_type'] = $r->tokenable_type;
                $nestedData['tokenable_id'] = $r->tokenable_id;
                $nestedData['name'] = $r->name;
                $nestedData['last_used_at'] = $r->last_used_at->format('Y-m-d H:i:s');
                $nestedData['expires_at'] = $r->expires_at ? $r->expires_at->format('Y-m-d H:i:s') : '';
                $nestedData['created_at'] = $r->created_at->format('Y-m-d H:i:s');
                $nestedData['updated_at'] = $r->updated_at->format('Y-m-d H:i:s');
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
