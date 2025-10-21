<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GenerateImageApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => 'required|string|min:10|max:1000',
            'options' => 'sometimes|array',
            'options.n' => 'sometimes|integer|min:1|max:4',
            'options.size' => 'sometimes|string|in:256x256,512x512,1024x1024,1024x1792,1792x1024',
            'options.quality' => 'sometimes|string|in:standard,hd',
            'options.style' => 'sometimes|string|in:vivid,natural',
        ];
    }
}
