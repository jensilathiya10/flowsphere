<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AIService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Demo-only web UI for the API scaffold in app/Http/Controllers/*Controller.php.
 * Logs in the seeded demo user by convention (no auth UI exists yet) purely
 * so the FlowSphere feature set is visible in a browser without a token.
 */
class DemoDashboardController extends Controller
{
    public function __construct(protected ReportService $reports, protected AIService $ai) {}

    public function index()
    {
        $user = User::where('email', 'jensi@flowsphere.test')->first();

        if (! $user || ! $user->currentWorkspace) {
            return view('demo-dashboard', ['seeded' => false]);
        }

        Auth::login($user);

        $workspace = $user->currentWorkspace;

        $projects = $workspace->projects()->withCount('tasks')->get()->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'progress' => $p->progress,
            'members' => $workspace->members()->count(),
            'days_remaining' => $p->daysRemaining(),
            'health_score' => $this->ai->healthScore($p),
        ]);

        return view('demo-dashboard', [
            'seeded' => true,
            'user' => $user,
            'workspace' => $workspace,
            'summary' => $this->reports->dashboardSummary($workspace),
            'productivity' => $this->reports->productivityByMember($workspace),
            'projects' => $projects,
            'workload' => $this->reports->workloadByMember($workspace),
            'recommendations' => $this->reports->workloadRecommendations($workspace),
            'flowAiAnswer' => session('flowAiAnswer'),
        ]);
    }

    public function askFlowAI(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'question' => 'required|string|max:500',
        ]);

        $user = Auth::user() ?? tap(User::where('email', 'jensi@flowsphere.test')->firstOrFail(), fn ($u) => Auth::login($u));
        $project = $user->currentWorkspace->projects()->findOrFail($data['project_id']);

        $report = $this->ai->askFlowAI($project, $data['question'], $user);

        return redirect()->route('demo.dashboard')->with('flowAiAnswer', $report);
    }
}
