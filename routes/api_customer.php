<?php

use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\ContactController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\PackageController;
use App\Http\Controllers\Customer\VerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/customer')
    ->group(function () {
        Route::controller(VerificationController::class)
            ->middleware('throttle:10,1')
            ->group(function () {
                Route::post('verify', 'verify');
                Route::post('confirm', 'confirm');
            });

        Route::controller(AuthController::class)
            ->middleware('throttle:20,1')
            ->group(function () {
                Route::post('register', 'register');
                Route::post('login', 'login');
                Route::post('recover', 'recover');
                Route::post('logout', 'logout')->middleware('auth:sanctum');
            });

        Route::middleware('auth:sanctum')
            ->prefix('auth')
            ->group(function () {
                Route::get('dashboard', [DashboardController::class, 'index']);

                Route::controller(PackageController::class)
                    ->prefix('packages')
                    ->group(function () {
                        Route::get('', 'index');
                        Route::post('{id}/payment', 'payment')->where(['id' => '[0-9]+']);
                    });

                Route::controller(CustomerController::class)
                    ->group(function () {
                        Route::post('profile', 'update');
                        Route::delete('profile', 'delete');
                    });
            });

        Route::post('contacts', [ContactController::class, 'store']);
    });
