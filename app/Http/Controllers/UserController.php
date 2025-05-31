<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function status()
    {
        return response()->json([
            'message' => 'ログイン中のユーザー情報です。',
            'user' => auth()->user(),
        ]);
    }


public function index(Request $request)
{
    $user = auth()->user();

    if (!$user || $user->role !== 'admin') {
        return response()->json(['message' => '許可されていません'], 403);
    }


    $query = \App\Models\User::query();

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    return response()->json($query->paginate(10));
}

//     public function index(Request $request)
// {
//     $user = auth()->user();

//     if (!$user) {
//         return response()->json(['error' => '認証されていません'], 401);
//     }

//     if ($user->role !== 'admin') {
//         return response()->json(['message' => '許可されていません'], 403);
//     }
//     // // クエリビルダを使用して条件検索
//     $query = \App\Models\User::query();

//     if ($request->filled('name')) {
//         $query->where('name', 'like', '%' . $request->name . '%');
//     }

//     if ($request->filled('email')) {
//         $query->where('email', 'like', '%' . $request->email . '%');
//     }

//     if ($request->filled('role')) {
//         $query->where('role', $request->role);
//     }

//     // ページネーション（1ページ10件）
//     $users = $query->paginate(10);

//     // SQL確認
//     dd($query->toSql(), $query->getBindings());

//     return response()->json($users);
// }

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

public function restore($id)
{
    $auth = Auth::user();

    // 論理削除されたユーザーを取得
    $user = User::onlyTrashed()->findOrFail($id);

        if ($auth->role !== 'admin') {
        return response()->json(['message' => '許可されていません（管理者のみ復元可）'], 403);
    }

    $user->restore();

    return response()->json(['message' => 'ユーザーを復元しました']);
}

public function trashed()
{
    $auth = Auth::user();

    if ($auth->role !== 'admin') {
        return response()->json(['message' => '許可されていません（管理者のみ閲覧可）'], 403);
    }

    return response()->json(User::onlyTrashed()->paginate(10));
}

public function forceDelete($id)
{
    $auth = Auth::user();

    // 管理者以外は拒否
    if ($auth->role !== 'admin') {
        return response()->json(['message' => '許可されていません（管理者のみ完全削除可）'], 403);
    }

    // 論理削除済みのユーザーを取得
    $user = User::onlyTrashed()->findOrFail($id);

    $user->forceDelete();

    return response()->json(['message' => 'ユーザーを完全に削除しました']);
}
}