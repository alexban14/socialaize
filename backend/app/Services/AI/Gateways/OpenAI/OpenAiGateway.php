<?php

namespace App\Services\AI\Gateways\OpenAI;

use GuzzleHttp\Client;

class OpenAiGateway implements OpenAiGatewayInterface
{
    protected Client $client;

    public function __construct(
        protected readonly string $apiKey,
        protected readonly string $baseUrl,
    ) {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function chatCompletions(array $payload): array
    {
        $response = $this->client->post('chat/completions', [
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
