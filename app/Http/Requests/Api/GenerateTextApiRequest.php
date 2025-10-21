<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTextApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => 'required|string|min:10|max:5000',
            'options' => 'sometimes|array',
            'options.max_tokens' => 'sometimes|integer|min:50|max:4000',
            'options.temperature' => 'sometimes|numeric|min:0|max:2',
            'options.system' => 'sometimes|string|max:1000',
        ];
    }
}
