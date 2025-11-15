<?php

namespace App\Services\AI\Clients;

interface AiClientInterface
{
    public function synthesize(string $prompt, array $options = []): array;
}
