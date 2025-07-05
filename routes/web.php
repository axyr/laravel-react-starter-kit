<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/user', function (Request $request) {
    return $request->user() ? new UserResource($request->user()) : [];
});

Route::get('reset-password/{token}', function ($token, Request $request) {
    return redirect(frontendUrl("auth/reset-password/{$token}?email=" . $request->get('email')));
})->name('password.reset');
