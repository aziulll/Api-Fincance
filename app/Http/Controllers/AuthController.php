<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function auth(LoginRequest $request)
    {
        $credentials = $request->only("email", "password", "device_name");

        $user = User::where("email", $request->email)->first();

        Hash::check($request->password, $user->password);
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                "email" =>  "The provided credentials are incorret",
                "password" => "The provided credentials are incorret"
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user, 
            
        ]);
    }

    public function logout(Request $request)
    {
        
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}
