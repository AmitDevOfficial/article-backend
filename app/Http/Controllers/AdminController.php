<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function create_user(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required'
        ]);

        $exists = User::whereEmail($request->email)->exists();
        if($exists){
            return response()->json([
                'status' => false,
                'message' => 'Email already exists.'
            ], 409);
        }

        if($request->hasFile('image')){
            $filename = time().'_'.$request->image->getClientOriginalName();
            $request->image->storeAs('uploads', $filename, 'public');
        }else{
            $filename = 'default.png';
        }

        $user = new User;
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->city = $request['city'];
        $user->gender = $request['gender'];
        $user->image = $filename;
        $user->password = Hash::make($request['password']);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User created SuccessFully.',
            'data' => $user
        ], 200);
    }

   
}
