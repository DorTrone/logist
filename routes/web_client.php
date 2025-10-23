<?php
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\PageController;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::controller(HomeController::class)
    ->group(function () {
        Route::get('', 'index')->name('home');
        Route::get('locale/{locale}', [HomeController::class, 'locale'])->name('locale')->where(['locale', '[a-z]+']);
    });

Route::controller(PageController::class)
    ->group(function () {
        Route::get('privacy-policy', 'privacyPolicy')->name('privacyPolicy');
    });

Route::controller(ContactController::class)
    ->middleware('throttle:20,1')
    ->group(function () {
        Route::post('contact', 'store')->middleware(ProtectAgainstSpam::class)->name('contact');
    });