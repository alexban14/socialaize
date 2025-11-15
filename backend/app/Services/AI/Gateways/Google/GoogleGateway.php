<?php

namespace App\Services\AI\Gateways\Google;

use GuzzleHttp\Client;

class GoogleGateway implements GoogleGatewayInterface
{
    protected Client $client;

    public function __construct(
        protected readonly string $apiKey,
        protected readonly string $baseUrl,
    ) {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function generateContent(string $model, array $payload): array
    {
        $url = "models/{$model}:generateContent?key={$this->apiKey}";

        $response = $this->client->post($url, [
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
