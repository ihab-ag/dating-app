<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Picture;
use App\Models\user_favourites;

class UsersController extends Controller
{
    public function getUsers(){
        $user=auth::user();

        $long_max= $user->location->longitude +50;
        $lat_max= $user->location->latitude +50;
        $long_min= $user->location->longitude -50;
        $lat_min= $user->location->latitude -50;

        $users = DB::table('users')
        ->join('locations', 'users.id', '=', 'locations.user_id')
        ->join('pictures', 'users.id', '=', 'pictures.user_id')
        ->whereBetween('longitude', [$long_min, $long_max])
        ->whereBetween('latitude', [$lat_min, $lat_max])
        ->whereNot('id',$user->id)
        ->get();
        

        return response()->json($users);
        
    }

    public function getFavourites(){
            $user=auth::user();
            $res=[];
            foreach ($user->favourite as $favourite) {
                $res[]=$favourite;
            }
            return response()->json(['users'=>$res]);
    }

    public function addFavourite(Request $request){
        $user=auth::user();

        $fav = new user_favourites([
            'user_id'=>$user->id,
            'favourites_id'=>$request->id
        ]);

        $fav->save();
    }
}
