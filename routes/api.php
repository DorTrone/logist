<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WarehouseController;

require __DIR__ . '/api_customer.php';
require __DIR__ . '/api_user.php';

Route::apiResource('warehouses', WarehouseController::class);