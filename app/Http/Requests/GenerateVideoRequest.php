<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt'         => ['required', 'string', 'max:2000'],
            'generate_audio' => ['required', 'boolean'],
            'ratio'          => ['required', 'string', 'in:21:9,16:9,4:3,1:1,3:4,9:16'],
            'resolution'     => ['required', 'integer', 'in:480,720,1080'],
            'duration'       => ['required', 'integer', 'min:4', 'max:12'],
        ];
    }
}