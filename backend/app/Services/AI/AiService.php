<?php

namespace App\Services\AI;

use App\Models\User;
use GuzzleHttp\Client;

class AiService implements AiServiceInterface
{
    protected Client $client;
    protected string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
        ]);
        $this->apiKey = config('services.openai.api_key');
    }

    public function synthesizeProfileFromPost(User $user, string $postContent): void
    {
        if (empty($this->apiKey)) {
            return;
        }

        $prompt = "Based on the following post, update the user's profile. Extract key skills, interests, and generate a new, improved bio.\n\nPost: {$postContent}\n\nResponse should be a JSON object with 'bio', 'skills', and 'interests' keys. Skills and interests should be arrays of strings.";

        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a profile assistant. Respond with a JSON object.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $profileData = json_decode($data['choices'][0]['message']['content'], true);

            $activeProfile = $user->activeProfile;

            if ($activeProfile) {
                if (isset($profileData['bio'])) {
                    $activeProfile->bio = $profileData['bio'];
                }
                if (isset($profileData['skills']) && is_array($profileData['skills'])) {
                    $existingSkills = $activeProfile->skills ?? [];
                    $activeProfile->skills = array_values(array_unique(array_merge($existingSkills, $profileData['skills'])));
                }
                if (isset($profileData['interests']) && is_array($profileData['interests'])) {
                    $existingInterests = $activeProfile->interests ?? [];
                    $activeProfile->interests = array_values(array_unique(array_merge($existingInterests, $profileData['interests'])));
                }
                $activeProfile->save();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI profile synthesis failed: ' . $e->getMessage());
        }
    }
}
