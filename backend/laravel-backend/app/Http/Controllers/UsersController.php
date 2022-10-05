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
use App\Models\user_blocks;

class UsersController extends Controller
{
    public function getUsers(){
        $user=auth::user();

        
        $long= $user->location->longitude;
        $lat= $user->location->latitude;
        

        $users = DB::table('users')
        ->join('locations', 'users.id', '=', 'locations.user_id')
        ->join('pictures', 'users.id', '=', 'pictures.user_id')
        ->orderby(DB::raw('(POW((longitude-'.$long.'),2) + POW((latitude-'.$lat.'),2))'))
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

    public function addFavourites(Request $request){
        $user=auth::user();

        $fav = new user_favourites([
            'user_id'=>$user->id,
            'favourite_id'=>$request->id
        ]);

        $fav->save();
    }

    public function addBlock(Request $request){
        $user=auth::user();

        $blocked = new user_blocks([
            'user_id'=>$user->id,
            'blocked_id'=>$request->id
        ]);

        $blocked->save();
    }
}
