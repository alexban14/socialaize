<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateAiModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:update-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest AI models from providers and store them in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting AI model update...');

        $providers = config('ai.providers');

        foreach ($providers as $provider => $config) {
            $this->line("Fetching models for: {$provider}");

            try {
                $models = match ($provider) {
                    'openai', 'groq' => $this->fetchOpenAICompatibleModels($config),
                    'google' => $this->fetchGoogleModels($config),
                    default => [],
                };

                if (!empty($models)) {
                    foreach ($models as $modelName) {
                        AiModel::updateOrCreate(
                            ['provider' => $provider, 'name' => $modelName],
                            ['is_active' => true]
                        );
                    }
                    $this->info("Successfully stored ".count($models)." models for {$provider}.");
                } else {
                    $this->warn("No models found or fetched for {$provider}.");
                }
            } catch (\Exception $e) {
                $this->error("Failed to fetch models for {$provider}: ".$e->getMessage());
                Log::error("AI Model Fetch Error ({$provider}): ".$e->getMessage());
            }
        }

        $this->info('AI model update complete.');
        return 0;
    }

    private function fetchOpenAICompatibleModels(array $config): array
    {
        $response = Http::withToken($config['api_key'])
            ->get("{$config['base_url']}/models");

        if ($response->failed()) {
            throw new \Exception("API request failed with status {$response->status()}");
        }

        return collect($response->json()['data'])
            ->pluck('id')
            ->sort()
            ->values()
            ->all();
    }

    private function fetchGoogleModels(array $config): array
    {
        $response = Http::get("{$config['base_url']}/models?key={$config['api_key']}");

        if ($response->failed()) {
            throw new \Exception("API request failed with status {$response->status()}");
        }

        return collect($response->json()['models'])
            ->map(fn ($model) => str_replace('models/', '', $model['name']))
            ->sort()
            ->values()
            ->all();
    }
}
