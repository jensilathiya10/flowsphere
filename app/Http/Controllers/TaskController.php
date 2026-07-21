<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    /** GET /projects/{project}/tasks — Kanban board data (To Do / Doing / Done). */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json(
            $project->tasks()->with(['assignee', 'comments.user'])->orderBy('position')->get()
                ->groupBy('status')
        );
    }

    /** POST /projects/{project}/tasks */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $task = $project->tasks()->create($request->validated() + [
            'created_by' => $request->user()->id,
            'status' => 'todo',
        ]);

        if ($task->assignee_id) {
            $this->notifications->taskAssigned($task->assignee, $task);
        }

        return $request->wantsJson()
            ? response()->json($task, 201)
            : redirect()->route('projects.show', $project);
    }

    /** PATCH /tasks/{task} — used for status changes (drag & drop), checklist ticks, edits. */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,doing,done',
            'priority' => 'sometimes|in:low,medium,high',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'checklist' => 'nullable|array',
            'position' => 'sometimes|integer',
        ]);

        $wasAssignee = $task->assignee_id;

        if (($data['status'] ?? null) === 'done' && $task->status !== 'done') {
            $data['completed_at'] = now();
        }

        $task->update($data);

        if (! empty($data['assignee_id']) && $data['assignee_id'] !== $wasAssignee) {
            $this->notifications->taskAssigned($task->assignee, $task);
        }

        $task->project->recalculateProgress();

        return $request->wantsJson()
            ? response()->json($task->fresh())
            : redirect()->route('projects.show', $task->project);
    }

    /** DELETE /tasks/{task} */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $project = $task->project;
        $task->delete();
        $project->recalculateProgress();

        return response()->noContent();
    }

    /** POST /tasks/{task}/comments */
    public function comment(Request $request, Task $task)
    {
        $this->authorize('comment', $task);

        $data = $request->validate(['body' => 'required|string|max:2000']);

        $comment = $task->comments()->create($data + ['user_id' => $request->user()->id]);

        return response()->json($comment->load('user'), 201);
    }
}
