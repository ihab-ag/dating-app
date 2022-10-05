<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Picture;
use App\Models\Message;
use App\Models\user_favourites;
use App\Models\user_blocks;

class UsersController extends Controller
{
    public function getUsers(){
        $user=auth::user();

        $gender= $user->gender;
        $interest=$user->interest;
        $long= $user->location->longitude;
        $lat= $user->location->latitude;

        $block_exist=[];
        $blocks=$user->block;
        $blockedBy=$user->blockedby;

        foreach($blocks as $block){
            $block_exist[]=$block->id;
        }
        foreach($blockedBy as $block){
            $block_exist[]=$block->id;
        }

        $users = User::
        join('locations', 'users.id', '=', 'locations.user_id')
        ->join('pictures', 'users.id', '=', 'pictures.user_id')
        ->whereNot('id',$user->id)
        ->where('private',0);
        
        if($interest=="male"){
            $users= $users
            ->where('gender','male');
        }
        elseif($interest=="female"){
            $users= $users
            ->where('gender','female');
        }

        $users= $users
        ->orderby(DB::raw('(POW((longitude-'.$long.'),2) + POW((latitude-'.$lat.'),2))'))
        ->get();

        foreach($users as $key => $item){
            if(in_array($item->id,$block_exist)){
                unset($users[$key]);
                continue;
            }
            if($item->interest!=$gender&&$item->interest!='both')
                unset($users[$key]);
        }
        $response=[];

        foreach($users as $user){
            $response[]=$user;
        }
        return response()->json($response);
        
    }

    public function getFavourites(){
            $user=auth::user();

            $users = $user->favourite;

            return response()->json($users);

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

    public function getChat(Request $request){

        $user1=Auth::user();
        $user2=User::where('id',$request->id)->limit(1)->get();
        $user2= $user2[0];

        $chat1= $user1->chat->where('id',$user2->id);
        $chat2= $user2->chat->where('id',$user1->id);

        if(count($chat2)==1){
            $chat= $chat2;
        }
        elseif(count($chat1)==1){
            $chat= $chat1;
        }
        else{
            $user1->chat->attach($user2->id);
            $chat= $user1->chat->where('id',$user2->id);
        }

        foreach($chat as $item){
           $chat_id= $item->pivot->id; 
        }

        $response=[];
        $messages= Message::where('chat_id',$chat_id)->get();
        foreach($messages as $message){
            $response[]= $message;
        }
        return response()->json([
            'chat_id'=> $chat_id,
            'messages'=>$response
        ]);
    }

    public function sendMessage(Request $request){
        
        $user=Auth::user();

        $message = new Message([
            'user_id'=>$user->id,
            'chat_id'=>$request->chat_id,
            'messages'=>$request->message
        ]);

        $message->save();
    }
}
