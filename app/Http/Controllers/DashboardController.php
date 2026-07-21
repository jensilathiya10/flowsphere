<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reports,
        protected NotificationService $notifications,
    ) {}

    /**
     * GET /dashboard
     * Hero summary + the four floating cards (Projects, Tasks, Team, Deadlines).
     */
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        abort_if(! $workspace, 404, 'No active workspace. Create or select one first.');

        return response()->json([
            'workspace' => $workspace->only(['id', 'name', 'type']),
            'summary' => $this->reports->dashboardSummary($workspace),
            'productivity' => $this->reports->productivityByMember($workspace),
            'recent_activity' => $workspace->activities()->with('user')->take(10)->get(),
            'unread_notifications' => $this->notifications->unreadCount($request->user()),
        ]);
    }

    /** GET /dashboard — the Blade page behind the hero summary + four floating cards. */
    public function page(Request $request)
    {
        $workspace = $request->user()->currentWorkspace;

        if (! $workspace) {
            return redirect()->route('onboarding');
        }

        return view('dashboard', [
            'workspace' => $workspace,
            'summary' => $this->reports->dashboardSummary($workspace),
            'productivity' => $this->reports->productivityByMember($workspace),
            'recentActivity' => $workspace->activities()->with('user')->take(10)->get(),
            'unreadNotifications' => $this->notifications->unreadCount($request->user()),
        ]);
    }
}
