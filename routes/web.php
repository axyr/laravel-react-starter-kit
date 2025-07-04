<?php

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:sanctum')->get('api/user', function () {
    return new UserResource(request()->user());
});
