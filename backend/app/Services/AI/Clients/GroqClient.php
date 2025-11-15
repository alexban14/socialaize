<?php

namespace App\Services\AI\Clients;

use App\Services\AI\Gateways\Groq\GroqGatewayInterface;
use Illuminate\Support\Arr;

class GroqClient implements AiClientInterface
{
    public function __construct(
        protected readonly GroqGatewayInterface $gateway,
        protected readonly array $config,
    ) {
    }

    public function synthesize(string $prompt, array $options = []): array
    {
        $modelConfig = $this->config['models']['synthesis'] ?? [];
        $model = $modelConfig['model'] ?? $this->config['models']['default'] ?? 'llama3-8b-8192';
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
