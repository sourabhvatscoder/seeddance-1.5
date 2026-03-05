<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_generations', function (Blueprint $table) {
            $table->id();

            $table->text('prompt_text');

            $table->enum('status', ['processing', 'success', 'error'])
                  ->default('processing')
                  ->index();

            $table->string('external_job_id')->nullable()->index();

            $table->string('seeddance_video_id')->nullable()->index();

            $table->text('video_url')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_generations');
    }
};