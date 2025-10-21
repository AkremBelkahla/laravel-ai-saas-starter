<?php

namespace App\Http\Controllers;

use App\Domain\Ai\Services\ImageGenerator;
use App\Http\Requests\GenerateImageRequest;
use App\Models\AiJob;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImageStudioController extends Controller
{
    public function __construct(
        protected ImageGenerator $imageGenerator
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('ImageStudio/Index');
    }

    public function generate(GenerateImageRequest $request)
    {
        $team = $request->user()->currentTeam();

        try {
            $job = $this->imageGenerator->generateAsync(
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

    public function gallery(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        $jobs = AiJob::where('team_id', $team->id)
            ->where('type', 'image')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return Inertia::render('ImageStudio/Gallery', [
            'jobs' => $jobs,
        ]);
    }

    public function show(Request $request, AiJob $job): Response
    {
        $team = $request->user()->currentTeam();

        if ($job->team_id !== $team->id) {
            abort(403);
        }

        return Inertia::render('ImageStudio/Show', [
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
