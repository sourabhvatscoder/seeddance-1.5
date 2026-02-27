<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVideoRequest;
use Illuminate\Http\JsonResponse;

class GenerateVideoController extends Controller
{
    public function __invoke(GenerateVideoRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Temporary debug response.',
            'payload' => $request->validated(),
        ], 202);
    }
}
