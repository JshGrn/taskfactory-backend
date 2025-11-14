<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string']
        ]);

        if(Auth::attempt($request->only('email', 'password')) === false){
            return response()->json([
                'message' => 'The email or password is incorrect.'
            ], 401);
        }

        session()->regenerate();

        return response()->json([
            'message' => 'User authenticated successfully.'
        ]);
    }
}
