<?php

namespace App\Services\AI\Gateways\Google;

interface GoogleGatewayInterface
{
    public function generateContent(string $model, array $payload): array;
}
