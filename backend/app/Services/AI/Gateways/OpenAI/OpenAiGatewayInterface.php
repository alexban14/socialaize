<?php

namespace App\Services\AI\Gateways\OpenAI;

interface OpenAiGatewayInterface
{
    public function chatCompletions(array $payload): array;
}
