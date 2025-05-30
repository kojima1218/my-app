<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        return Project::all(); // 管理者：全件
    }

    return Project::where('user_id', $user->id)->get(); // 一般ユーザー：自分の分
}

public function store(Request $request)
{
     
    $user = Auth::user();

    if ($user->role !== 'user') {
        return response()->json(['message' => '許可されていません（管理者は作成不可）'], 403);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $project = Project::create([
        'user_id' => $user->id,
        'name' => $validated['name'],
        'description' => $validated['description'],
        'project_key' => Str::random(16),
    ]);

    return response()->json($project, 201);
}

public function show(Project $project)
{
    $user = Auth::user();

    if ($user->role !== 'admin' && $user->id !== $project->user_id) {
        return response()->json(['message' => '許可されていません'], 403);
    }

    return response()->json($project);
}

public function update(Request $request, Project $project)
{
    $user = Auth::user();

    if ($user->id !== $project->user_id) {
        return response()->json(['message' => '許可されていません（自分のプロジェクトのみ更新可）'], 403);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $project->update($validated);

    return response()->json(['message' => '更新しました']);
}

public function destroy(Project $project)
{
    $user = Auth::user();

    if ($user->id !== $project->user_id) {
        return response()->json(['message' => '許可されていません（自分のプロジェクトのみ削除可）'], 403);
    }

    $project->delete();

    return response()->json(['message' => '削除しました']);
}
}
