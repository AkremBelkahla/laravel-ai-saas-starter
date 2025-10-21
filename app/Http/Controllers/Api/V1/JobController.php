<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function show(Request $request, string $id): JsonResponse
    {
        $team = $request->user()->currentTeam();

        $job = AiJob::where('id', $id)
            ->where('team_id', $team->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $job->id,
                'type' => $job->type,
                'status' => $job->status,
                'prompt' => $job->prompt,
                'result' => $job->result,
                'tokens_used' => $job->tokens_used,
                'actual_cost' => $job->actual_cost,
                'model' => $job->model,
                'error' => $job->error,
                'created_at' => $job->created_at,
                'completed_at' => $job->completed_at,
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam();

        $jobs = AiJob::where('team_id', $team->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }
}
