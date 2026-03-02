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
            'prompt' => 'A girl holding a fox, the girl opens her eyes, looks gently at the camera, the fox hugs affectionately, the camera slowly pulls out, the girl’s hair is blown by the wind, and the sound of the wind can be heard',
            'image_url' => 'https://ark-doc.tos-ap-southeast-1.bytepluses.com/doc_image/i2v_foxrgirl.png',
            'generate_audio' => true,
            'ratio' => 'adaptive',
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
            'status' => $videoGeneration->status,
        ], 202);
    }

    public function getStatus(int $id, SeedanceService $seedanceService): JsonResponse
    {
        $videoGeneration = VideoGeneration::findOrFail($id);
        $videoStatus = $seedanceService->getVideoStatus($videoGeneration->seeddance_video_id);

        return response()->json([
            'id' => $videoGeneration->id,
            'status' => $videoStatus['status'] ?? $videoGeneration->status,
            'video_url' => $videoStatus['video_url'] ?? null,
            'error_message' => $videoStatus['error'] ?? null,
        ]);
    }
}
