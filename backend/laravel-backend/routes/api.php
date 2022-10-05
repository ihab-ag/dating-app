<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post("/signup", [AuthController::class, "signUp"]);
Route::post("/signin", [AuthController::class, "signIn"]);

Route::group(["middleware" => "auth"], function(){
    Route::post("/refresh", [AuthController::class, "refresh"]);
    Route::post("/get-user", [AuthController::class, "getUser"]);
    Route::post("/update-user", [AuthController::class, "updateUser"]);
    Route::post("/get-users", [UsersController::class, "getUsers"]);
    Route::post("/favourites", [UsersController::class, "getFavourites"]);
    Route::post("/add-favourite", [UsersController::class, "addFavourites"]);
    Route::post("/add-block", [UsersController::class, "addBlock"]);
    Route::post("/chats", [UsersController::class, "getChat"]);

});
