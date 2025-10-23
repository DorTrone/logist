<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Error;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:50'],
            'surname' => ['required', 'string', 'max:50'],
            'username' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'code' => ['required', 'integer', 'between:10000,99999'],
            'auth_method' => ['required', 'integer', 'between:0,1'],
            'language' => ['required', 'integer', 'between:0,3'], // en, tm, ru, cn
            'platform' => ['required', 'integer', 'between:1,2'], // android, ios
        ]);
        $validator->sometimes('username', ['integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/', Rule::unique('customers')], function (Fluent $input) {
            return $input->auth_method == 0;
        });
        $validator->sometimes('username', ['string', 'email', 'max:50', Rule::unique('customers')], function (Fluent $input) {
            return $input->auth_method == 1;
        });
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Auth register 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $verification = Verification::where('username', $request->username)
            ->where('code', $request->code)
            ->whereIn('status', [0, 1, 3])
            ->where('method', $request->auth_method)
            ->where('updated_at', '>', now()->subMinutes(3))
            ->orderBy('id', 'desc')
            ->first();

        if ($verification) {
            $verification->status = 2;
            $verification->update();
        } else {
            Error::create([
                'title' => 'Customer Auth register 422',
                'body' => 'Invalid verification code',
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Invalid verification code',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer = Customer::create([
            'code' => str()->random(5),
            'name' => $request->name,
            'surname' => $request->surname,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'auth_method' => $request->auth_method,
            'language' => $request->language,
            'platform' => $request->platform,
        ]);

        $customer->code = 'SZD' . strval($customer->id + 1000);
        $customer->ext_keyword = str(strval($customer->id + 1000)
            . ' SZD' . strval($customer->id + 1000)
            . ' ' . $customer->name
            . ' ' . $customer->surname
            . ' ' . $customer->username)->squish()->lower()->slug(' ');
        $customer->update();

        $token = $customer->createToken(['Web', 'Android', 'iOS'][$request->platform])->plainTextToken;

        return $this->respondWithToken($customer, $token);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'auth_method' => ['required', 'integer', 'between:0,2'],
            'language' => ['required', 'integer', 'between:0,3'], // en, tm, ru, cn
            'platform' => ['required', 'integer', 'between:1,2'], // android, ios
        ]);
        $validator->sometimes('username', ['integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'], function (Fluent $input) {
            return $input->auth_method == 0;
        });
        $validator->sometimes('username', ['string', 'email', 'max:50'], function (Fluent $input) {
            return $input->auth_method == 1;
        });
        $validator->sometimes('username', ['string', 'max:50'], function (Fluent $input) {
            return $input->auth_method == 2;
        });
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Auth login 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer = Customer::where('username', $request->username)->first();
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $customer->language = $request->language;
        $customer->platform = $request->platform;
        $customer->update();

        $token = $customer->createToken(['Web', 'Android', 'iOS'][$request->platform])->plainTextToken;

        return $this->respondWithToken($customer, $token);
    }

    public function recover(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'code' => ['required', 'integer', 'between:10000,99999'],
            'auth_method' => ['required', 'integer', 'between:0,1'],
            'language' => ['required', 'integer', 'between:0,3'], // en, tm, ru, cn
            'platform' => ['required', 'integer', 'between:1,2'], // android, ios
        ]);
        $validator->sometimes('username', ['integer', 'regex:/^(6[0-5]\d{6}|71\d{6})$/'], function (Fluent $input) {
            return $input->auth_method == 0;
        });
        $validator->sometimes('username', ['string', 'email', 'max:50'], function (Fluent $input) {
            return $input->auth_method == 1;
        });
        if ($validator->fails()) {
            Error::create([
                'title' => 'Customer Auth recover 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $verification = Verification::where('username', $request->username)
            ->where('code', $request->code)
            ->whereIn('status', [0, 1, 3])
            ->where('method', $request->auth_method)
            ->where('updated_at', '>', now()->subMinutes(3))
            ->orderBy('id', 'desc')
            ->first();

        if ($verification) {
            $verification->status = 2;
            $verification->update();
        } else {
            Error::create([
                'title' => 'Customer Auth recover 422',
                'body' => 'Invalid verification code',
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Invalid verification code',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer = Customer::where('username', $request->username)->first();
        if (!$customer) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $customer->password = bcrypt($request->password);
        $customer->language = $request->language;
        $customer->platform = $request->platform;
        $customer->update();

        $token = $customer->createToken(['Web', 'Android', 'iOS'][$request->platform])->plainTextToken;

        return $this->respondWithToken($customer, $token);
    }

    public function logout()
    {
        auth('customer_api')->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
        ], Response::HTTP_OK);
    }

    protected function respondWithToken($customer, $token)
    {
        return response()->json([
            'status' => 1,
            'id' => $customer->id,
            'code' => $customer->code,
            'name' => $customer->name,
            'surname' => $customer->surname,
            'username' => $customer->username,
            'accessToken' => $token,
        ]);
    }
}
