<?php

namespace App\Services;

use App\Models\Project;
use App\Models\AiReport;
use App\Models\User;

/**
 * Powers the "Flow AI" project assistant and the Project Health Score.
 *
 * The scoring/diagnosis logic here is deliberately simple and dependency-free
 * so the feature works immediately on seeded data. Swap the body of
 * askFlowAI() for a real LLM call (e.g. via an HTTP client to your model
 * provider) when you're ready — the surrounding data-gathering stays the same.
 */
class AIService
{
    /**
     * Answer a free-text question about a project (e.g. "Why is this project delayed?").
     */
    public function askFlowAI(Project $project, string $question, ?User $askedBy = null): AiReport
    {
        $diagnosis = $this->diagnose($project);

        return AiReport::create([
            'project_id' => $project->id,
            'requested_by' => $askedBy?->id,
            'question' => $question,
            'answer' => $diagnosis['answer'],
            'recommendations' => $diagnosis['recommendations'],
            'health_score' => $diagnosis['health_score'],
        ]);
    }

    /**
     * Compute the 0-100 Project Health Score from completion, deadline, workload, and activity.
     */
    public function healthScore(Project $project): int
    {
        return $this->diagnose($project)['health_score'];
    }

    protected function diagnose(Project $project): array
    {
        $tasks = $project->tasks()->get();
        $total = $tasks->count();
        $done = $tasks->where('status', 'done')->count();
        $overdue = $tasks->filter(fn ($t) => $t->isOverdue())->count();

        $completionScore = $total > 0 ? ($done / $total) * 100 : 100;
        $deadlinePenalty = min(40, $overdue * 8);
        $daysRemaining = $project->daysRemaining();
        $deadlinePenalty += ($daysRemaining !== null && $daysRemaining < 0) ? 15 : 0;

        $workload = (new ReportService)->workloadByMember($project->workspace);
        $overloaded = $workload->filter(fn ($w) => $w['utilization'] >= 100)->count();
        $workloadPenalty = min(20, $overloaded * 10);

        $recentActivityCount = $project->workspace->activities()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $activityPenalty = $recentActivityCount === 0 ? 10 : 0;

        $score = (int) max(0, min(100, round($completionScore - $deadlinePenalty - $workloadPenalty - $activityPenalty)));

        $recommendations = [];
        $answerParts = [];

        if ($overdue > 0) {
            $answerParts[] = "{$overdue} task(s) are past their due date.";
        }
        if ($overloaded > 0) {
            $names = $workload->filter(fn ($w) => $w['utilization'] >= 100)->pluck('name')->implode(', ');
            $answerParts[] = "{$overloaded} team member(s) are at or above full capacity ({$names}).";
            $recommendations[] = 'Rebalance tasks away from overloaded members toward teammates with spare capacity.';
        }
        if ($daysRemaining !== null && $daysRemaining < 0) {
            $answerParts[] = 'The project due date has already passed.';
            $recommendations[] = 'Move the deadline out by ' . max(3, abs((int) $daysRemaining)) . ' day(s) or reduce scope.';
        } elseif ($daysRemaining !== null && $daysRemaining <= 3 && $completionScore < 80) {
            $answerParts[] = "Only {$daysRemaining} day(s) remain and completion is at " . round($completionScore) . '%.';
            $recommendations[] = 'Move the deadline out by 3 days or add support to at-risk tasks.';
        }
        if ($recentActivityCount === 0) {
            $answerParts[] = 'No activity has been logged on this project in the last 7 days.';
            $recommendations[] = 'Check in with the team — the project may be stalled.';
        }
        if (empty($answerParts)) {
            $answerParts[] = 'No blockers detected. Tasks are on track and the team is at healthy capacity.';
        }

        return [
            'answer' => implode(' ', $answerParts),
            'recommendations' => $recommendations,
            'health_score' => $score,
        ];
    }

    public function healthLabel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Healthy',
            $score >= 50 => 'At Risk',
            default => 'Critical',
        };
    }
}
