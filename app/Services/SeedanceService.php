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

    public function getVideoStatus(string $videoId): array
    {
        // poll until the Seedance task either returns a video_url or an error
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
        $statusUrl = $baseUrl . $path . '/' . $videoId;

        $interval = (int) config('services.seedance.retry_sleep_ms', 500) * 1000; // microseconds

        while (true) {
            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->timeout((int) config('services.seedance.timeout_seconds', 30))
                    ->get($statusUrl)
                    ->throw();

                $body = $response->json();
                $status = $body['status'] ?? null;

                if ($status === 'succeeded' && isset($body['content']['video_url'])) {
                    $videoUrl = $body['content']['video_url'];
                    \App\Models\VideoGeneration::where('id', $videoId)
                        ->update(['video_url' => $videoUrl, 'status' => 'success']);

                    return [
                        'success' => true,
                        'status' => $response->status(),
                        'data' => $body,
                        'error' => null,
                    ];
                }

                if ($status === 'error' || isset($body['error'])) {
                    $errorMsg = $body['error']['message'] ?? 'unknown error';
                    \App\Models\VideoGeneration::where('id', $videoId)
                        ->update(['status' => 'error', 'error_message' => $errorMsg]);

                    return [
                        'success' => false,
                        'status' => $response->status(),
                        'data' => $body,
                        'error' => ['message' => $errorMsg, 'type' => 'task_error'],
                    ];
                }

                // still processing, sleep then retry
                usleep($interval);
                continue;
            } catch (RequestException $exception) {
                // network/transient error, try again after sleep
                usleep($interval);
                continue;
            } catch (ConnectionException $exception) {
                usleep($interval);
                continue;
            }
        }
    }
}
