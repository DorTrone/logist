<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate; 

class WarehouseController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Привязываем политику автоматически ко всем методам CRUD
        $this->authorizeResource(Warehouse::class, 'warehouse');
    }

    /**
     * Display a listing of the warehouses.
     */
    public function index()
    {
        $this->authorize('warehouses', \App\Models\User::class);
        $warehouses = Warehouse::all();
        return view('admin.warehouses.index', compact('warehouses'));
        
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        $this->authorize('warehouses', \App\Models\User::class);
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('warehouses', \App\Models\User::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'meta' => 'nullable|json',
        ]);

        Warehouse::create($validated);
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        $this->authorize('warehouses', \App\Models\User::class);
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $this->authorize('warehouses', \App\Models\User::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'meta' => 'nullable|json',
        ]);

        $warehouse->update($validated);
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $this->authorize('warehouses', \App\Models\User::class);
        $warehouse->delete();
        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse deleted.');
    }
    public function api(Request $request)
{
    $warehouses = Warehouse::query();
    
    return datatables($warehouses)
        ->editColumn('actions', function ($warehouse) {
            return view('admin.warehouses.actions', compact('warehouse'))->render();
        })
        ->rawColumns(['actions'])
        ->make(true);
}
}
