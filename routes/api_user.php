<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\PackageController;
use App\Http\Controllers\User\TransportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/user')
    ->group(function () {
        Route::controller(AuthController::class)
            ->middleware('throttle:20,1')
            ->group(function () {
                Route::post('login', 'login');
                Route::post('logout', 'logout')->middleware('auth:sanctum');
            });

        Route::middleware('auth:sanctum')
            ->prefix('auth')
            ->group(function () {
                Route::controller(DashboardController::class)
                    ->group(function () {
                        Route::get('dashboard', 'index');
                    });

                Route::controller(PackageController::class)
                    ->prefix('packages')
                    ->group(function () {
                        Route::get('', 'index'); // 1
                        Route::post('', 'store'); // 2
                        Route::post('{id}', 'status')->where(['id' => '[0-9]+']); // 3
                        Route::post('{id}/payment', 'payment')->where(['id' => '[0-9]+']); // 4
                        Route::post('image', 'image');
                        Route::post('quick', 'quick'); // 3
                    });

                Route::controller(TransportController::class)
                    ->prefix('transports')
                    ->group(function () {
                        Route::get('', 'index'); // 5
                    });

                Route::controller(CustomerController::class)
                    ->prefix('customers')
                    ->group(function () {
                        Route::get('', 'index'); // 8
                        Route::post('', 'store'); // 9
                    });
            });
    });
