<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'max:50'],
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Customer update 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $obj = auth('customer_api')->user();
        $obj->name = $request->name;
        $obj->surname = $request->surname;
        if (isset($request->password)) {
            $obj->password = bcrypt($request->password);
        }
        $obj->update();

        $obj->ext_keyword = str(strval($obj->id + 1000)
            . ' SZD' . strval($obj->id + 1000)
            . ' ' . $obj->name
            . ' ' . $obj->surname
            . ' ' . $obj->username)->squish()->lower()->slug(' ');
        $obj->update();

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $obj->id,
                'code' => $obj->code,
                'name' => $obj->name,
                'surname' => $obj->surname,
                'username' => $obj->username,
            ],
            'message' => 'Customer updated',
        ], Response::HTTP_OK);
    }

    public function delete()
    {
        $obj = auth('customer_api')->user();
        $obj->username = now()->format('ymdHis') . '_' . $obj->username;
        $obj->update();

        $obj->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Customer deleted',
        ], Response::HTTP_OK);
    }
}
