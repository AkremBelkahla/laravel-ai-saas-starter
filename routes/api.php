<?php

use App\Http\Controllers\Api\V1\ImageGenerationController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\TextGenerationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Text Generation
    Route::post('/generate-text', [TextGenerationController::class, 'generate'])
        ->middleware('throttle:api');

    // Image Generation
    Route::post('/generate-image', [ImageGenerationController::class, 'generate'])
        ->middleware('throttle:api');

    // Jobs
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{id}', [JobController::class, 'show']);
});
