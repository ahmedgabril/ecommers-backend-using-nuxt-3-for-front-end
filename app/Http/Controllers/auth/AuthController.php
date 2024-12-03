<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function authuser(Request $request)
    {

        return $request->user();
    }
    public function register(Request $request)
    {

        $request->validate([
            "name" => ["required", "string", "lowercase", "max:225"],
            "email" => ["required", "string", "lowercase", "max:225", "email", "unique:" . User::class],
            'password' => ['required'],
        ]);


        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->string('password')),
        ]);

        // Handle remember me

        $token = $user->createToken('authToken')->plainTextToken;

        // Handle remember me




        return response()->json(['message' => 'Registration successful', 'user' => $user])
            ->cookie('token', $token, 60 * 24 * 30, '/', null, true, true);
    }


    public function login(Request $request)
    {
        $request->validate([
            "email" => ["email", "required", "string", "exists:users,email", "lowercase"],
            "password" => "required"

        ]);


        $user = User::where("email", $request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {


            throw ValidationException::withMessages([


                "email" => ["The provided credentials are incorrect."]
            ]);
        }


        $token = $user->createToken('authToken')->plainTextToken;

        // // Handle remember me
        // if ($request->remember) {
        //     $user->setRememberToken(Str::random(60));
        //     // $user->update(['api_token' => $token]);
        //     $user->save();
        // }
        // // return response()->json(data: ['message' => 'Registration successful', 'user' => $user, 'token' => $token]);


        return response()->json(['user' => $user])
            ->cookie('token', $token, 60 * 24 * 30, '/', null, true, true);
    }







    public function logout()
    {

        $cookie = Cookie::forget('token');

        return response()->json(['message' => 'Logged out successfully'])
            ->withCookie($cookie);
    }
}
