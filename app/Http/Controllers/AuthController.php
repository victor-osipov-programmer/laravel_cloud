<?php

namespace App\Http\Controllers;

use App\Exceptions\LoginFailed;
use App\Models\User;
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
        $user = auth()->user();
        $user->token = $token;
        $user->save();

        return response([
            'success' => true,
            'message' => 'Success',
            'token' => $token
        ]);
    }

    function logout() {
        $user = Auth::user();
        if (!$user) {
            throw new LoginFailed();
        }

        $user->update([
            'token' => null
        ]);

        return response([
            'success' => true,
            'message' => 'Logout'
        ]);
    }
}
