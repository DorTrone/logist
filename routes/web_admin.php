<?php

use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\AuthAttemptController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ErrorController;
use App\Http\Controllers\Admin\IpAddressController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PackagePanelController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\UserAgentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\VisitorPanelController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// ─── AUTH ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('lgn25', [AuthController::class, 'create'])->name('login');
    Route::post('lgn25', [AuthController::class, 'store'])->middleware(ProtectAgainstSpam::class);
});

Route::middleware('auth:web')->group(function () {
    Route::post('lgt25', [AuthController::class, 'destroy'])->name('logout');
});

// ─── ADMIN PANEL ─────────────────────────────────────────────────────
Route::middleware('auth:web')->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('packagesPanel', [PackagePanelController::class, 'index'])
        ->middleware('can:packagesPanel')->name('packagesPanel.index');

    Route::get('visitorsPanel', [VisitorPanelController::class, 'index'])
        ->middleware('can:visitorsPanel')->name('visitorsPanel.index');

    Route::get('adminPanel', [AdminPanelController::class, 'index'])
        ->middleware('can:adminPanel')->name('adminPanel.index');

    // ERRORS & TOKENS
    Route::controller(ErrorController::class)->middleware('can:errors')
        ->prefix('errors')->name('errors.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(TokenController::class)->middleware('can:tokens')
        ->prefix('tokens')->name('tokens.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    // PACKAGES & TRANSPORTS
    Route::controller(PackageController::class)->middleware('can:packages')
        ->prefix('packages')->name('packages.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(TransportController::class)->middleware('can:transports')
        ->prefix('transports')->name('transports.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    // CUSTOMERS
    Route::controller(CustomerController::class)->middleware('can:customers')
        ->prefix('customers')->name('customers.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
            Route::post('api', 'api')->name('api');
        });

    // VERIFICATIONS & CONTACTS
    Route::controller(VerificationController::class)->middleware('can:verifications')
        ->prefix('verifications')->name('verifications.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(ContactController::class)->middleware('can:contacts')
        ->prefix('contacts')->name('contacts.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
            Route::post('archive', 'archive')->name('archive');
            Route::post('api', 'api')->name('api');
        });

    // BANNERS
    Route::controller(BannerController::class)->middleware('can:banners')
        ->prefix('banners')->name('banners.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
            Route::post('up', 'up')->name('up');
            Route::post('down', 'down')->name('down');
        });

    // NOTIFICATIONS
    Route::controller(NotificationController::class)->middleware('can:notifications')
        ->prefix('notifications')->name('notifications.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(PushNotificationController::class)->middleware('can:pushNotifications')
        ->prefix('pushNotifications')->name('pushNotifications.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    // TASKS, USERS, CONFIGS
    Route::controller(TaskController::class)->middleware('can:tasks')
        ->prefix('tasks')->name('tasks.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
        });

    Route::controller(UserController::class)->middleware('can:users')
        ->prefix('users')->name('users.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->name('edit')->whereNumber('id');
            Route::put('{id}', 'update')->name('update')->whereNumber('id');
            Route::delete('{id}', 'destroy')->name('destroy')->whereNumber('id');
        });

    Route::controller(ConfigController::class)->middleware('can:configs')
        ->prefix('configs')->name('configs.')->group(function () {
            Route::get('edit', 'edit')->name('edit');
            Route::put('', 'update')->name('update');
        });

    // IP ADDRESSES, USER AGENTS, VISITORS
    Route::controller(IpAddressController::class)->middleware('can:ipAddresses')
        ->prefix('ipAddresses')->name('ipAddresses.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('disabled', 'disabled')->name('disabled');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(UserAgentController::class)->middleware('can:userAgents')
        ->prefix('userAgents')->name('userAgents.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('disabled', 'disabled')->name('disabled');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(AuthAttemptController::class)->middleware('can:authAttempts')
        ->prefix('authAttempts')->name('authAttempts.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('api', 'api')->name('api');
        });

    Route::controller(VisitorController::class)->middleware('can:visitors')
        ->prefix('visitors')->name('visitors.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('disabled', 'disabled')->name('disabled');
            Route::post('api', 'api')->name('api');
        });

    // WAREHOUSES
    Route::controller(WarehouseController::class)
    // ->middleware('can:warehouses')
        ->prefix('warehouses')->name('warehouses.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('', 'store')->name('store');
            Route::get('{warehouse}/edit', 'edit')->name('edit');
            Route::put('{warehouse}', 'update')->name('update');
            Route::delete('{warehouse}', 'destroy')->name('destroy');
            Route::post('api', 'api')->name('api');
        });

});
