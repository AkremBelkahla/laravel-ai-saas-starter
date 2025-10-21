<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Ai\Services\ImageGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GenerateImageApiRequest;
use Illuminate\Http\JsonResponse;

class ImageGenerationController extends Controller
{
    public function __construct(
        protected ImageGenerator $imageGenerator
    ) {
    }

    public function generate(GenerateImageApiRequest $request): JsonResponse
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
                'data' => [
                    'job_id' => $job->id,
                    'status' => $job->status,
                    'estimated_cost' => $job->estimated_cost,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
