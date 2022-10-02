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
        ]);

        $user= new User([
            'name' => $request -> input("name"),
            'email' => $request -> input("email"),
            'password' => Hash::make($request -> input("password"))
        ]);

        $user->save();
        return response()->json([
            "message"=>"Success"
        ]);
    }

    function signIn(Request $request){

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
        ]);
        
        $credentials= $request->only('email','password');

        $token = Auth::attempt($credentials);
    }
}
