<?php

namespace App\Services\AI\Clients;

use App\Services\AI\Gateways\Google\GoogleGatewayInterface;
use Illuminate\Support\Arr;

class GoogleClient implements AiClientInterface
{
    public function __construct(
        protected readonly GoogleGatewayInterface $gateway,
        protected readonly array $config,
    ) {
    }

    public function synthesize(string $prompt, array $options = []): array
    {
        $modelConfig = $this->config['models']['synthesis'] ?? [];
        $model = $modelConfig['model'] ?? $this->config['models']['default'] ?? 'gemini-1.5-flash';
        $requestOptions = array_merge($modelConfig['options'] ?? [], $options);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'You are a profile assistant. Respond with a valid JSON object. Do not use markdown.']
                    ]
                ],
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => $requestOptions,
        ];

        $data = $this->gateway->generateContent($model, $payload);

        $content = Arr::get($data, 'candidates.0.content.parts.0.text', '{}');
        
        $cleanedContent = str_replace(['```json', '```'], '', $content);

        return json_decode($cleanedContent, true);
    }
}
