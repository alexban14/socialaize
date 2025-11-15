<?php

namespace App\Services\AI\Gateways\Groq;

interface GroqGatewayInterface
{
    public function chatCompletions(array $payload): array;
}
