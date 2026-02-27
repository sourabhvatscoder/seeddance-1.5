<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVideoRequest;
use App\Jobs\GenerateVideoJob;
use App\Models\VideoGeneration;
use Illuminate\Http\JsonResponse;

class GenerateVideoController extends Controller
{
    public function __invoke(GenerateVideoRequest $request): JsonResponse
    {
        $videoGeneration = VideoGeneration::create([
            'prompt_text' => $request->validated('prompt'),
            'status' => 'processing',
        ]);

        GenerateVideoJob::dispatch($videoGeneration->id);

        return response()->json([
            'message' => 'Video generation request queued.',
            'id' => $videoGeneration->id,
            'status' => $videoGeneration->status,
        ], 202);
    }
}
