<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/create',[UserController::class,"create"])->middleware('guest:sanctum');
Route::post('/user/login',[UserController::class,"login"])->middleware('guest:sanctum');
Route::delete('/user/logout/{token?}',[UserController::class,"logout"]);

Route::get('/test',
function(){
    return response()->json(['message' => 'Test route is working']);
}
);

Route::get('/user',[UserController::class,"index"])->middleware('auth:sanctum');