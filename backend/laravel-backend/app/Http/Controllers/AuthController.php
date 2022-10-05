<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Picture;

class AuthController extends Controller
{
    function signUp(Request $request){
    // validation
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
    // create new user
        $user= new User([
            'name' => $request -> input("name"),
            'email' => $request -> input("email"),
            'password' => Hash::make($request -> input("password")),
            'age' => $request -> input("age"),
            'gender' => $request -> input("gender"),
            'interest' => $request -> input("interest"),
            'bio' => " ",
        ]);
    // create location for user
        $location= new Location([
            'longitude' => $request -> input('longitude'),
            'latitude' => $request -> input('latitude'),
        ]);

        $picture= new Picture([
            'url'=>" "
        ]);
    // save user and location
        $user->save();
        $user->location()->save($location);
        $user->picture()->save($picture);
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
    // get email and pass
        $credentials = $request->only('email', 'password');
    // authenticate user
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
    // return authenticated user with jwt
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }
    // refresh token
    public function refresh(){

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
    // get user
    public function getUser(){
        $user= Auth::user();
        return response()->json([
            'status'=>'success',
            'user' => [$user,
            $user->location[0],
            $user->picture[0]],
        ]);
    }
     // update user
     public function updateUser(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'gender' => 'required',
            'interest' => 'required'
        ]);
        $user= Auth::user();

        $user->update([
            'name'=> $request->name,
            'bio'=> $request->bio,
            'private'=> $request->private,
            'gender'=> $request->gender,
            'interest'=> $request->interest
        ]);

        $location=[
            'latitude'=> $request->latitude,
            'longitude'=> $request->longitude,
        ];

        $id = $user->id;
        $url = $request->url;
        $url= getUrl($url,$id);
        $picture= ['url'=> $url ];

        $user->location()->update($location);
        $user->picture()->update($picture);

        return response()->json([
            "status"=>"success"
        ]);
     }
}
function getUrl($url,$id){
   $decoder = base64_decode($url);
   $img = imagecreatefromstring($decoder);
   $url="../public/images/" . $id . ".jpg";
   if($img){
       imagejpeg($img, $url);
       return $url;
   }
   else
   return "not found";
}
