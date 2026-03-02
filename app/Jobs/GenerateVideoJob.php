<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $videoGenerationId)
    {
    }

    public function handle(\App\Services\SeedanceService $service): void
    {
        // poll the Seedance API until the video is ready or an error occurs
        $service->getVideoStatus((string) $this->videoGenerationId);
    }
}
