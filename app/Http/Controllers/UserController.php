<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function status()
    {
        return response()->json([
            'message' => 'ログイン中のユーザー情報です。',
            'user' => auth()->user(),
        ]);
    }
}