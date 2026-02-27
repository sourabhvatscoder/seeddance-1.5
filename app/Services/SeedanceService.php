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

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.seedance.timeout_seconds', 30))
                ->retry(
                    (int) config('services.seedance.retry_attempts', 3),
                    (int) config('services.seedance.retry_sleep_ms', 500)
                )
                ->post($baseUrl . $path, $payload)
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
}
