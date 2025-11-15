<?php

return [
    'default' => env('AI_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1/',
            'models' => [
                'default' => 'gpt-4o-mini',
                'synthesis' => [
                    'model' => 'gpt-4o-mini',
                    'options' => [
                        'temperature' => 0.7,
                        'response_format' => ['type' => 'json_object'],
                    ],
                ],
            ],
        ],
        'google' => [
            'api_key' => env('GOOGLE_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/',
            'models' => [
                'default' => 'gemini-2.5-flash',
                'synthesis' => [
                    'model' => 'gemini-2.5-flash',
                    'options' => [
                        'temperature' => 0.7,
                    ],
                ],
            ],
        ],
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1/',
            'models' => [
                'default' => 'llama-3.3-70b-versatile',
                'synthesis' => [
                    'model' => 'llama-3.3-70b-versatile',
                    'options' => [
                        'temperature' => 0.7,
                        'response_format' => ['type' => 'json_object'],
                    ],
                ],
            ],
        ],
    ],
];
