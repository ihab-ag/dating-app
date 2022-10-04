<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Picture;

class UsersController extends Controller
{
    public function getUsers(){
        $user=auth::user();

        $long_max= $user->location->longitude +20;
        $lat_max= $user->location->latitude +20;
        $long_min= $user->location->longitude -20;
        $lat_min= $user->location->latitude -20;

        $users = DB::table('users')
        ->join('locations', 'users.id', '=', 'locations.user_id')
        ->join('pictures', 'users.id', '=', 'pictures.user_id')
        ->whereBetween('longitude', [$long_min, $long_max])
        ->whereBetween('latitude', [$lat_min, $lat_max])
        ->whereNot('id',$user->id)
        ->hide('password')
        ->get();

        return response()->json($users);
        
    }
}
