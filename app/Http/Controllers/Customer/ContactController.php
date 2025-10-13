<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'],
            'email' => ['nullable', 'string', 'email', 'max:50'],
            'message' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Contact store 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = new Contact();
        $obj->name = $request->name;
        $obj->surname = $request->surname;
        $obj->phone = $request->phone ?: null;
        $obj->email = $request->email ?: null;
        $obj->message = $request->message
            . (auth('customer_api')->check() ? '<br>' . auth('customer_api')->user()->getName() : '');
        $obj->save();

        return response()->json([
            'status' => 1,
            'message' => 'Message sent',
        ], Response::HTTP_OK);
    }
}
