<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AddTokenToHeaders;
use App\Http\Controllers\auth\AuthController;

Route::middleware([AddTokenToHeaders::class,"auth:sanctum"])->group(function () {
    Route::get('/authuser', [AuthController::class, 'authuser']);

    Route::post('/logout', [AuthController::class, 'logout']);

});


Route::post("/login",[AuthController::class,'login'])->name("login");
Route::post("/register",[AuthController::class,'register'])->name("register");
