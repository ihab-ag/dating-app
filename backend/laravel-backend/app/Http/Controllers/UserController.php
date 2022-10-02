<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    function signUp(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $user= new User([
            'name'=$request->input("name"),
            'email'=$request->input("email"),
            'password'=bcrypt($request->input("password")),
        ]);

        $user->save();
        return response()->json([
            "message"=>"Success"
        ]);
    }
}
