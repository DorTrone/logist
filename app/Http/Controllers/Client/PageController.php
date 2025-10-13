<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        return view('client.page.privacyPolicy');
    }
}
