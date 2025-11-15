<?php

namespace App\Services\AI\Clients;

use App\Services\AI\Gateways\OpenAI\OpenAiGatewayInterface;
use Illuminate\Support\Arr;

class OpenAiClient implements AiClientInterface
{
    public function __construct(
        protected readonly OpenAiGatewayInterface $gateway,
        protected readonly array $config,
    ) {
    }

    public function synthesize(string $prompt, array $options = []): array
    {
        $modelConfig = $this->config['models']['synthesis'] ?? [];
        $model = $modelConfig['model'] ?? $this->config['models']['default'] ?? 'gpt-4o-mini';
        $requestOptions = array_merge($modelConfig['options'] ?? [], $options);

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a profile assistant. Respond with a JSON object.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            ...$requestOptions,
        ];

        $data = $this->gateway->chatCompletions($payload);

        return json_decode(Arr::get($data, 'choices.0.message.content', '{}'), true);
    }
}