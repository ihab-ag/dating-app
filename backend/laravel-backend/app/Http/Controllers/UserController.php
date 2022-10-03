<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    function signUp(Request $request){

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password'=>[
                'required',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[A-Z]/'
            ],
            'age' => 'required',
            'gender' => 'required',
            'interest' => 'required'
        ]);

        $user= new User([
            'name' => $request -> input("name"),
            'email' => $request -> input("email"),
            'password' => Hash::make($request -> input("password")),
            'age' => $request -> input("age"),
            'gender' => $request -> input("gender"),
            'interest' => $request -> input("interest"),
            'bio' => $request -> input("bio"),
        ]);

        $user->save();
        return response()->json([
            "message"=>"Success",
            200
        ]);
    }

    function signIn(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }
}
