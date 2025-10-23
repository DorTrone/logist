<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('client.home.index');
    }

    public function locale($locale)
    {
        $locale = in_array($locale, ['tm', 'ru', 'cn']) ? $locale : 'en';
        session()->put('locale', $locale);

        return redirect()->back();
    }
}
