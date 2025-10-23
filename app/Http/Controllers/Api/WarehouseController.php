<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Http\Resources\WarehouseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::all();
        return WarehouseResource::collection($warehouses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:20',
            'meta' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $warehouse = Warehouse::create($request->all());
        
        return new WarehouseResource($warehouse);
    }

    public function show($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return new WarehouseResource($warehouse);
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string',
            'postal_code' => 'sometimes|required|string|max:20',
            'meta' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $warehouse->update($request->all());
        
        return new WarehouseResource($warehouse);
    }

    public function destroy($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();
        
        return response()->json(null, 204);
    }
}