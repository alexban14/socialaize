<?php

namespace App\Services\AI\Factories;

use App\Enums\AiProvider;
use App\Models\AiModel;
use App\Services\AI\Clients\AiClientInterface;
use App\Services\AI\Clients\GoogleClient;
use App\Services\AI\Clients\GroqClient;
use App\Services\AI\Clients\OpenAiClient;
use App\Services\AI\Gateways\Google\GoogleGateway;
use App\Services\AI\Gateways\Groq\GroqGateway;
use App\Services\AI\Gateways\OpenAI\OpenAiGateway;
use Illuminate\Contracts\Config\Repository as Config;
use InvalidArgumentException;

class AiClientFactory
{
    public function __construct(
        protected readonly Config $config,
        protected readonly AiModel $aiModel
    ) {
    }

    public function make(?string $provider = null): AiClientInterface
    {
        $provider = $provider ?? $this->config->get('ai.default');
        $providerEnum = AiProvider::tryFrom($provider);

        if (!$providerEnum) {
            throw new InvalidArgumentException("Unsupported AI provider: {$provider}");
        }

        $config = $this->config->get("ai.providers.{$provider}");

        if (!$config) {
            throw new InvalidArgumentException("Configuration for AI provider '{$provider}' not found.");
        }

        // Fetch models from the database, falling back to config
        $models = $this->aiModel->where('provider', $provider)->where('is_active', true)->pluck('name')->toArray();
        if (empty($models)) {
            $models = collect($config['models'])->pluck('model')->filter()->all();
        }
        $config['models']['available'] = $models;


        return match ($providerEnum) {
            AiProvider::OPENAI => new OpenAiClient(new OpenAiGateway($config['api_key'], $config['base_url']), $config),
            AiProvider::GOOGLE => new GoogleClient(new GoogleGateway($config['api_key'], $config['base_url']), $config),
            AiProvider::GROQ => new GroqClient(new GroqGateway($config['api_key'], $config['base_url']), $config),
        };
    }
}
