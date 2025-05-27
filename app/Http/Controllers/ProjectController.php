<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = $request->user()->projects()->create([
            'name' => $request->name,
            'description' => $request->description,
            'project_key' => Str::uuid(),
        ]);

        return response()->json($project, 201);
    }
}
