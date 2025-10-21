<?php

namespace App\Http\Controllers;

use App\Domain\Ai\Services\TextGenerator;
use App\Http\Requests\GenerateTextRequest;
use App\Models\AiJob;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CopyStudioController extends Controller
{
    public function __construct(
        protected TextGenerator $textGenerator
    ) {
    }

    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        $templates = Template::where('team_id', $team->id)
            ->orWhere('is_public', true)
            ->where('type', 'text')
            ->get();

        return Inertia::render('CopyStudio/Index', [
            'templates' => $templates,
        ]);
    }

    public function generate(GenerateTextRequest $request)
    {
        $team = $request->user()->currentTeam();

        try {
            $job = $this->textGenerator->generateAsync(
                $team,
                $request->input('prompt'),
                $request->input('options', [])
            );

            return response()->json([
                'success' => true,
                'job' => $job,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        $jobs = AiJob::where('team_id', $team->id)
            ->where('type', 'text')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('CopyStudio/History', [
            'jobs' => $jobs,
        ]);
    }

    public function show(Request $request, AiJob $job): Response
    {
        $team = $request->user()->currentTeam();

        if ($job->team_id !== $team->id) {
            abort(403);
        }

        return Inertia::render('CopyStudio/Show', [
            'job' => $job,
        ]);
    }

    public function status(Request $request, AiJob $job)
    {
        $team = $request->user()->currentTeam();

        if ($job->team_id !== $team->id) {
            abort(403);
        }

        return response()->json([
            'job' => $job->fresh(),
        ]);
    }
}
