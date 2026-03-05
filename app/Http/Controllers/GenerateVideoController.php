<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVideoRequest;
use App\Models\VideoGeneration;
use App\Services\SeedanceService;
use Illuminate\Http\JsonResponse;

class GenerateVideoController extends Controller
{
    public function __invoke(GenerateVideoRequest $request, SeedanceService $seedanceService): JsonResponse
    {
        $seedanceResponse = $seedanceService->generateVideo([
            'prompt' => $request->validated('prompt'),
            // 'image_url' => 'https://ark-doc.tos-ap-southeast-1.bytepluses.com/doc_image/i2v_foxrgirl.png',
            // 'generate_audio' => true,
            'ratio' => '16:9',
            'duration' => 4,
            'watermark' => false,
        ]);

        $videoGeneration = VideoGeneration::create([
            'prompt_text' => $request->validated('prompt'),
            'status' => 'processing',
            'seeddance_video_id' => $seedanceResponse['data']['id'] ?? null,
            'error_message' => $seedanceResponse['error']['message'] ?? null,
        ]);

        return response()->json([
            'message' => 'Video generation request accepted.',
            'id' => $videoGeneration->id,
            'seeddance_video_id' => $seedanceResponse['data']['id'] ?? null,
            'status' => $videoGeneration->status,
        ], 202);
    }

    public function checkVideoStatus(string $videoId, SeedanceService $seedanceService): JsonResponse
    {
        $videoUrl = $seedanceService->getVideoStatus($videoId);

        // If a URL is returned, generation is successful
        if ($videoUrl !== null) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'video_url' => $videoUrl,
            ], 200);
        }

        // Since the function returns null for both "processing" and "error",
        // we check the database to tell the frontend exactly what's happening.
        $record = VideoGeneration::where('seeddance_video_id', $videoId)->first();

        // If the database status was updated to 'error'
        if ($record && $record->status === 'error') {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $record->error_message ?? 'An unknown error occurred.',
            ], 400); 
        }

        // Otherwise, it's still processing
        return response()->json([
            'success' => true,
            'status' => 'processing',
            'message' => 'Video is still being generated. Please wait.',
        ], 200); // 200 OK (or 202 Accepted)
    }
}
