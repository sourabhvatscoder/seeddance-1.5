<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('video_generations', function (Blueprint $table) {
            // Add boolean column, default to false
            $table->boolean('is_saved')->default(false)->after('error_message');
        });
    }

    public function down(): void
    {
        Schema::table('video_generations', function (Blueprint $table) {
            $table->dropColumn('is_saved');
        });
    }
};