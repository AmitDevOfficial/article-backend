<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::whereEmail($request->email)->first();
        if(!$user){
            return response()->json([
                'status' => False,
                'message' => 'Invaild Email',
                'data' => $user
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'stasus' => False,
                'message' => 'Invaild Password',
                'data' => $user
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'status' => true,
            'message' => 'Login Successfully',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logout SuccessFully'
        ]);
    }
}
