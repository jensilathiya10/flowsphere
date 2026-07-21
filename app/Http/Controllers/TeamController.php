<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Services\ReportService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(protected ReportService $reports) {}

    /** GET /workspaces/{workspace}/team — profile cards: role, status, tasks, completed. */
    public function index(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return response()->json(
            $workspace->members()->get()->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'title' => $member->pivot->title ?? $member->job_title,
                'role' => $member->pivot->role,
                'online' => $member->isOnline(),
                'tasks' => $member->assignedTasks()->count(),
                'completed' => $member->assignedTasks()->where('status', 'done')->count(),
            ])
        );
    }

    /** GET /workspaces/{workspace}/team/workload — Smart Workload Management bars + recommendations. */
    public function workload(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return response()->json([
            'workload' => $this->reports->workloadByMember($workspace),
            'recommendations' => $this->reports->workloadRecommendations($workspace),
        ]);
    }

    /** POST /workspaces/{workspace}/team/invite */
    public function invite(Request $request, Workspace $workspace)
    {
        $this->authorize('manageTeam', $workspace);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,manager,member,client',
            'title' => 'nullable|string|max:255',
        ]);

        $workspace->members()->syncWithoutDetaching([
            $data['user_id'] => [
                'role' => $data['role'],
                'title' => $data['title'] ?? null,
                'joined_at' => now(),
            ],
        ]);

        return response()->json(['status' => 'invited'], 201);
    }
}
