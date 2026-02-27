<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt_text',
        'status',
        'external_job_id',
        'video_url',
        'error_message',
    ];
}
