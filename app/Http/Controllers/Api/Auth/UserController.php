<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(auth()->user());
    }
}
