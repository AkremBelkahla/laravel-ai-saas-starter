<?php

namespace App\Http\Controllers;

use App\Domain\Billing\Services\CreditService;
use App\Models\AiJob;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected CreditService $creditService
    ) {
    }

    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        // Recent jobs
        $recentJobs = AiJob::where('team_id', $team->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Stats
        $stats = [
            'total_jobs' => AiJob::where('team_id', $team->id)->count(),
            'completed_jobs' => AiJob::where('team_id', $team->id)->where('status', 'completed')->count(),
            'failed_jobs' => AiJob::where('team_id', $team->id)->where('status', 'failed')->count(),
            'text_jobs' => AiJob::where('team_id', $team->id)->where('type', 'text')->count(),
            'image_jobs' => AiJob::where('team_id', $team->id)->where('type', 'image')->count(),
        ];

        // Credit usage this month
        $usageStats = $this->creditService->getUsageStats(
            $team,
            now()->startOfMonth(),
            now()
        );

        // Chart data - last 7 days
        $chartData = $this->getChartData($team);

        return Inertia::render('Dashboard/Index', [
            'recentJobs' => $recentJobs,
            'stats' => $stats,
            'usageStats' => $usageStats,
            'chartData' => $chartData,
        ]);
    }

    protected function getChartData($team): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) use ($team) {
            $date = now()->subDays($daysAgo);

            return [
                'date' => $date->format('M d'),
                'text_jobs' => AiJob::where('team_id', $team->id)
                    ->where('type', 'text')
                    ->whereDate('created_at', $date)
                    ->count(),
                'image_jobs' => AiJob::where('team_id', $team->id)
                    ->where('type', 'image')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        });

        return $days->toArray();
    }
}
