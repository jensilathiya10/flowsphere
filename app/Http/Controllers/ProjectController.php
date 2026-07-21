<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Models\Project;
use App\Models\Workspace;
use App\Services\AIService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(protected AIService $ai) {}

    /** GET /workspaces/{workspace}/projects — project cards: name, progress, members, days left. */
    public function index(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return response()->json($this->projectCards($workspace));
    }

    /** GET /projects — the Blade project-cards page for the user's current workspace. */
    public function pageIndex(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return redirect()->route('onboarding');
        }

        return view('projects.index', [
            'workspace' => $workspace,
            'projects' => $this->projectCards($workspace),
        ]);
    }

    /** GET /projects/create — new project form. */
    public function create(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return redirect()->route('onboarding');
        }

        return view('projects.create', ['workspace' => $workspace]);
    }

    protected function projectCards(Workspace $workspace)
    {
        return $workspace->projects()
            ->withCount('tasks')
            ->get()
            ->map(fn (Project $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'progress' => $p->progress,
                'members' => $p->workspace->members()->count(),
                'days_remaining' => $p->daysRemaining(),
                'health_score' => $p->health_score,
                'health_label' => $p->health_score !== null ? $this->ai->healthLabel($p->health_score) : null,
            ]);
    }

    /** GET /projects/{project} — overview tab data. */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json([
            'project' => $project,
            'tasks_by_status' => [
                'todo' => $project->tasks()->where('status', 'todo')->get(),
                'doing' => $project->tasks()->where('status', 'doing')->get(),
                'done' => $project->tasks()->where('status', 'done')->get(),
            ],
            'health_score' => $this->ai->healthScore($project),
        ]);
    }

    /** GET /projects/{project} (web) — detail page: Overview / Tasks / Team / Files / Activity tabs. */
    public function pageShow(Project $project)
    {
        $this->authorize('view', $project);

        $healthScore = $this->ai->healthScore($project);

        return view('projects.show', [
            'project' => $project,
            'tasksByStatus' => [
                'todo' => $project->tasks()->with('assignee')->orderBy('position')->where('status', 'todo')->get(),
                'doing' => $project->tasks()->with('assignee')->orderBy('position')->where('status', 'doing')->get(),
                'done' => $project->tasks()->with('assignee')->orderBy('position')->where('status', 'done')->get(),
            ],
            'healthScore' => $healthScore,
            'healthLabel' => $this->ai->healthLabel($healthScore),
            'members' => $project->workspace->members()->get(),
            'attachments' => $project->attachments()->with('uploader')->latest()->get(),
        ]);
    }

    /** POST /workspaces/{workspace}/projects */
    public function store(StoreProjectRequest $request, Workspace $workspace)
    {
        $this->authorize('update', $workspace);

        $project = $workspace->projects()->create($request->validated() + [
            'owner_id' => $request->user()->id,
            'status' => 'planning',
        ]);

        return $request->wantsJson()
            ? response()->json($project, 201)
            : redirect()->route('projects.show', $project);
    }

    /** PATCH /projects/{project} */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:planning,active,on_hold,completed,archived',
            'due_date' => 'nullable|date',
        ]);

        $project->update($data);

        return response()->json($project->fresh());
    }

    /** DELETE /projects/{project} */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return response()->noContent();
    }

    /** POST /projects/{project}/ask-flow-ai — "Why is this project delayed?" */
    public function askFlowAI(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $data = $request->validate(['question' => 'required|string|max:500']);

        $report = $this->ai->askFlowAI($project, $data['question'], $request->user());

        return response()->json($report);
    }
}
