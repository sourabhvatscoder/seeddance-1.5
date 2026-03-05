<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class SeedanceService
{
    public function generateVideo(array $payload): array
    {
        $apiKey = (string) config('services.seedance.api_key');

        if ($apiKey === '') {
            return [
                'success' => false,
                'status' => null,
                'data' => null,
                'error' => [
                    'message' => 'Seedance API key is not configured.',
                    'type' => 'configuration_error',
                ],
            ];
        }

        $baseUrl = rtrim((string) config('services.seedance.base_url'), '/');
        $path = '/' . ltrim((string) config('services.seedance.generate_video_path'), '/');

        $textPrompt = (string) ($payload['prompt'] ?? '');
        $imageUrl = $payload['image_url'] ?? null;

        $requestPayload = [
            'model' => 'seedance-1-5-pro-251215',
            'content' => array_values(array_filter([
                [
                    'type' => 'text',
                    'text' => $textPrompt,
                ],
                is_string($imageUrl) && $imageUrl !== ''
                    ? [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => $imageUrl,
                        ],
                    ]
                    : null,
            ])),
            'generate_audio' => (bool) ($payload['generate_audio'] ?? true),
            'ratio' => (string) ($payload['ratio'] ?? 'adaptive'),
            'duration' => (int) ($payload['duration'] ?? 4),
            'watermark' => (bool) ($payload['watermark'] ?? false),
        ];

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.seedance.timeout_seconds', 30))
                ->retry(
                    (int) config('services.seedance.retry_attempts', 3),
                    (int) config('services.seedance.retry_sleep_ms', 500)
                )
                ->post($baseUrl . $path, $requestPayload)
                ->throw();

            return [
                'success' => true,
                'status' => $response->status(),
                'data' => $response->json(),
                'error' => null,
            ];
        } catch (ConnectionException $exception) {
            return [
                'success' => false,
                'status' => null,
                'data' => null,
                'error' => [
                    'message' => $exception->getMessage(),
                    'type' => 'connection_error',
                ],
            ];
        } catch (RequestException $exception) {
            return [
                'success' => false,
                'status' => $exception->response?->status(),
                'data' => $exception->response?->json(),
                'error' => [
                    'message' => $exception->getMessage(),
                    'type' => 'request_error',
                ],
            ];
        }
    }

    public function getVideoStatus(string $videoId): ?string
    {
        $apiKey = (string) config('services.seedance.api_key');
        if ($apiKey === '') {
            return null;
        }

        $baseUrl = rtrim((string) config('services.seedance.base_url'), '/');
        $path = '/' . ltrim((string) config('services.seedance.generate_video_path'), '/');
        $statusUrl = $baseUrl . $path . '/' . $videoId;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout((int) config('services.seedance.timeout_seconds', 30))
                ->get($statusUrl)
                ->throw();

            $body = $response->json();
            $status = $body['status'] ?? null;

            // Return URL if successful
            if ($status === 'succeeded' && !empty($body['content']['video_url'])) {
                $videoUrl = $body['content']['video_url'];
                
                \App\Models\VideoGeneration::where('seeddance_video_id', $videoId)
                    ->update(['video_url' => $videoUrl, 'status' => 'success']);

                return $videoUrl;
            }

            // Handle API-side errors
            if ($status === 'error' || isset($body['error'])) {
                $errorMsg = $body['error']['message'] ?? 'unknown error';
                
                \App\Models\VideoGeneration::where('seeddance_video_id', $videoId)
                    ->update(['status' => 'error', 'error_message' => $errorMsg]);

                return null;
            }

            // Return null for 'processing' or any other intermediate status
            return null;

        } catch (\Exception $exception) {
            return null;
        }
    }
}
