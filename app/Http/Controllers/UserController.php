<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function status()
    {
        return response()->json([
            'message' => 'ログイン中のユーザー情報です。',
            'user' => auth()->user(),
        ]);
    }

    public function index()
{
    if (auth()->user()->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }

    return response()->json(User::all());
}

    public function store(Request $request)
{
    if (auth()->user()->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'user',
    ]);

    return response()->json($user, 201);
}
    public function show(User $user)
{
    $loginUser = auth()->user();

    if ($loginUser->id !== $user->id && $loginUser->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }

    return response()->json($user);
}

    public function update(Request $request, User $user)
{
    $loginUser = auth()->user();

    if ($loginUser->id !== $user->id && $loginUser->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }

    $user->update($request->only(['name', 'email']));
    return response()->json($user);
}

    public function destroy(User $user)
{
    $loginUser = auth()->user();

    if ($loginUser->id !== $user->id && $loginUser->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }

    $user->delete();
    return response()->json(['message' => 'ユーザーを削除しました']);
}
}