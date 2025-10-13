<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'],
            'email' => ['nullable', 'string', 'email', 'max:50'],
            'message' => ['required', 'string', 'max:255'],
        ]);

        $obj = new Contact();
        $obj->name = $request->name;
        $obj->surname = $request->surname;
        $obj->phone = $request->phone ?: null;
        $obj->email = $request->email ?: null;
        $obj->message = $request->message;
        $obj->save();

        return to_route('home')
            ->with([
                'success' => 'Thank you for contacting us. We’ve received your message and your contact details. Our team will get in touch with you shortly.',
            ]);
    }
}
