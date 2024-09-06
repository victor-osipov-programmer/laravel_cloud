<?php

namespace App\Http\Controllers;

use App\Exceptions\LoginFailed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function authorization()
    {
        $data = request(['email', 'password']);

        if (!$token = Auth::attempt($data)) {
            throw new LoginFailed();
        }

        return response([
            'success' => true,
            'message' => 'Success',
            'token' => $token
        ]);
    }
}
