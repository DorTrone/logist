<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Error;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'max:50'],
            'language' => ['required', 'integer', 'between:0,3'], // en, tm, ru, cn
            'platform' => ['required', 'integer', 'between:1,2'], // android, ios
        ]);
        if ($validator->fails()) {
            Error::create([
                'title' => 'User Auth login 422',
                'body' => $validator->errors(),
            ]);

            return response()->json([
                'status' => 0,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->language = $request->language;
        $user->platform = $request->platform;
        $user->update();

        $token = $user->createToken(['Web', 'Android', 'iOS'][$request->platform])->plainTextToken;

        return $this->respondWithToken($user, $token);
    }

    public function logout()
    {
        auth('api')->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
        ], Response::HTTP_OK);
    }

    protected function respondWithToken($user, $token)
    {
        return response()->json([
            'status' => 1,
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'accessToken' => $token,
        ]);
    }
}
