<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class authController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails())
        {
            return response()->json([
            "statusCode"=> 404,
    "message"=> $validator->errors()->all()[0],
    "error"=> "Conflict",
    'status'=>404,
        ],400);

        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['statusCode' => 401,'message' => 'invalid credentials','error' => 'Unauthorized'],401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json(['accessToken' => $accessToken]);

    }
}
