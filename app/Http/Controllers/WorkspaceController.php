<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /** GET /workspaces — list workspaces the current user belongs to. */
    public function index(Request $request)
    {
        return response()->json($request->user()->workspaces()->withCount(['members', 'projects'])->get());
    }

    /** POST /workspaces — Step 2 of onboarding: Company / Startup / Personal / Freelance. */
    public function store(StoreWorkspaceRequest $request)
    {
        $data = $request->validated();

        $workspace = Workspace::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(6),
            'type' => $data['type'],
            'owner_id' => $request->user()->id,
        ]);

        $workspace->members()->attach($request->user()->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        $request->user()->update(['current_workspace_id' => $workspace->id]);

        return $request->wantsJson()
            ? response()->json($workspace, 201)
            : redirect()->route('dashboard');
    }

    /** PATCH /workspaces/{workspace}/switch — set as the user's active workspace. */
    public function switchTo(Request $request, Workspace $workspace)
    {
        abort_unless($workspace->members()->where('user_id', $request->user()->id)->exists(), 403);

        $request->user()->update(['current_workspace_id' => $workspace->id]);

        return response()->json($workspace);
    }
}
