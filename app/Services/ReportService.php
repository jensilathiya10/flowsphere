<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Powers Smart Workload Management, the Reports module, and the workload
 * inputs used by AIService for the Project Health Score.
 */
class ReportService
{
    /**
     * Per-member workload as a percentage of capacity, based on open assigned tasks.
     * Each open task is assumed to cost ~4 hours unless refined further.
     */
    public function workloadByMember(Workspace $workspace, float $hoursPerTask = 4.0): Collection
    {
        return $workspace->members()->get()->map(function (User $member) use ($hoursPerTask) {
            $openTasks = $member->assignedTasks()
                ->whereIn('status', ['todo', 'doing'])
                ->whereHas('project', fn ($q) => $q->where('workspace_id', $member->pivot->workspace_id))
                ->count();

            $capacity = $member->pivot->capacity_hours_per_week ?: 40;
            $estimatedHours = $openTasks * $hoursPerTask;
            $utilization = $capacity > 0 ? (int) round(($estimatedHours / $capacity) * 100) : 0;

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'open_tasks' => $openTasks,
                'capacity_hours' => $capacity,
                'estimated_hours' => $estimatedHours,
                'utilization' => $utilization,
            ];
        });
    }

    /** Suggests moving tasks from overloaded members to members with spare capacity. */
    public function workloadRecommendations(Workspace $workspace): array
    {
        $workload = $this->workloadByMember($workspace)->sortByDesc('utilization')->values();
        $overloaded = $workload->filter(fn ($w) => $w['utilization'] > 90);
        $underloaded = $workload->filter(fn ($w) => $w['utilization'] < 70)->sortBy('utilization')->values();

        $recommendations = [];
        foreach ($overloaded as $over) {
            $target = $underloaded->first();
            if (! $target) {
                break;
            }
            $tasksToMove = max(1, (int) round(($over['utilization'] - 90) / 25));
            $recommendations[] = "Move {$tasksToMove} task(s) from {$over['name']} to {$target['name']}.";
        }

        return $recommendations;
    }

    /** Productivity percentage per member: completed vs. assigned tasks, for the Reports module. */
    public function productivityByMember(Workspace $workspace): Collection
    {
        return $workspace->members()->get()->map(function (User $member) {
            $assigned = $member->assignedTasks()->count();
            $completed = $member->assignedTasks()->where('status', 'done')->count();

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'assigned' => $assigned,
                'completed' => $completed,
                'productivity' => $assigned > 0 ? (int) round(($completed / $assigned) * 100) : 0,
            ];
        })->sortByDesc('productivity')->values();
    }

    /** Dashboard summary cards: active projects, completed tasks, member count, upcoming deadlines. */
    public function dashboardSummary(Workspace $workspace): array
    {
        return [
            'active_projects' => $workspace->projects()->where('status', 'active')->count(),
            'completed_tasks' => \App\Models\Task::whereHas('project', fn ($q) => $q->where('workspace_id', $workspace->id))
                ->where('status', 'done')->count(),
            'members' => $workspace->members()->count(),
            'deadlines_this_week' => $workspace->calendarEvents()
                ->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }
}
