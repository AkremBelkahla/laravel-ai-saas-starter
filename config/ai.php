<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI services (OpenAI, etc.)
    |
    */

    'default_driver' => env('AI_DRIVER', 'openai'),

    'drivers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
            'models' => [
                'text' => env('OPENAI_TEXT_MODEL', 'gpt-4-turbo-preview'),
                'image' => env('OPENAI_IMAGE_MODEL', 'dall-e-3'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Credit Costs
    |--------------------------------------------------------------------------
    |
    | Cost in credits for each AI operation
    |
    */

    'costs' => [
        'text_per_1k_tokens' => env('COST_TEXT_PER_1K_TOKENS', 10),
        'image' => [
            'small' => env('COST_IMAGE_SMALL', 100),    // 256x256, 512x512
            'medium' => env('COST_IMAGE_MEDIUM', 200),  // 1024x1024
            'large' => env('COST_IMAGE_LARGE', 300),    // 1024x1792, 1792x1024
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monthly Credit Allocations
    |--------------------------------------------------------------------------
    |
    | Credits allocated per plan per month
    |
    */

    'credits' => [
        'free' => [
            'text' => env('CREDITS_FREE_TEXT', 10000),
            'image' => env('CREDITS_FREE_IMAGE', 5),
        ],
        'pro' => [
            'text' => env('CREDITS_PRO_TEXT', 100000),
            'image' => env('CREDITS_PRO_IMAGE', 100),
        ],
        'team' => [
            'text' => env('CREDITS_TEAM_TEXT', 500000),
            'image' => env('CREDITS_TEAM_IMAGE', 500),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    |
    | API rate limits per minute by plan
    |
    */

    'rate_limits' => [
        'free' => env('RATE_LIMIT_FREE', 60),
        'pro' => env('RATE_LIMIT_PRO', 600),
        'team' => env('RATE_LIMIT_TEAM', 1200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'text' => [
            'max_tokens' => 2000,
            'temperature' => 0.7,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ],
        'image' => [
            'size' => '1024x1024',
            'quality' => 'standard',
            'style' => 'vivid',
            'n' => 1,
        ],
    ],

];
