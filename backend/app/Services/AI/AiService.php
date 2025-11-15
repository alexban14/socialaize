<?php

namespace App\Services\AI;

use App\Models\User;
use App\Services\AI\Factories\AiClientFactory;
use App\Services\Interest\InterestServiceInterface;
use App\Services\Skill\SkillServiceInterface;
use Illuminate\Support\Facades\Log;

class AiService implements AiServiceInterface
{
    public function __construct(
        private readonly SkillServiceInterface $skillService,
        private readonly InterestServiceInterface $interestService,
        private readonly AiClientFactory $aiClientFactory
    ) {
    }

    public function synthesizeProfileFromPost(User $user, string $postContent, string $profileType): void
    {
        try {
            $client = $this->aiClientFactory->make();

            $profile = $user->profiles()->where('profile_type', $profileType)->first();

            if (!$profile) {
                Log::info("AI profile synthesis skipped: No '{$profileType}' profile for user {$user->id}.");
                return;
            }

            $skills = $profile->skills()->pluck('name')->implode(', ');
            $interests = $profile->interests()->pluck('name')->implode(', ');

            // Pre-evaluate expressions into simple variables for heredoc compatibility.
            $profileTitle = $profile->title ?? 'Not set';
            $profileBio = $profile->bio ?? 'Not set';
            $profileSkills = $skills ?: 'None';
            $profileInterests = $interests ?: 'None';

            $context = <<<CONTEXT
- **Title:** {$profileTitle}
- **Bio:** {$profileBio}
- **Skills:** {$profileSkills}
- **Interests:** {$profileInterests}
CONTEXT;

            $prompt = <<<PROMPT
**Goal:** Enhance the user's professional profile based on their latest post.

**Context: User's Current Profile ({$profileType})**
{$context}

**Task: Analyze the following post and update the profile.**
- **Post Content:** "{$postContent}"

**Instructions:**
1.  **Analyze:** Read the post content in the context of the user's current profile.
2.  **Generate Bio:** Write a new, improved bio (around 3-4 sentences) that synthesizes the user's existing profile with insights from the new post. The new bio should be engaging and reflect their expertise.
3.  **Extract Skills:** Identify and list key skills from the post. These can be new skills or reinforce existing ones.
4.  **Extract Interests:** Identify and list key interests or topics from the post.

**Output Format:**
Return a single, valid JSON object with the following keys:
- "bio": A string containing the new bio.
- "skills": An array of strings.
- "interests": An array of strings.
PROMPT;

            Log::debug("AiService prompt: " . $prompt);

            $profileData = $client->synthesize($prompt);

            Log::debug("Parsed profile data: " . json_encode($profileData, JSON_PRETTY_PRINT));

            if (isset($profileData['bio'])) {
                $profile->bio = $profileData['bio'];
            }
            if (isset($profileData['skills']) && is_array($profileData['skills'])) {
                foreach ($profileData['skills'] as $skillName) {
                    $skill = $this->skillService->findOrCreate($skillName);
                    $this->skillService->addSkillToProfile($skill, $profile);
                }
            }
            if (isset($profileData['interests']) && is_array($profileData['interests'])) {
                foreach ($profileData['interests'] as $interestName) {
                    $interest = $this->interestService->findOrCreate($interestName);
                    $this->interestService->addInterestToProfile($interest, $profile);
                }
            }
            $profile->save();

        } catch (\Exception $e) {
            Log::error('AI profile synthesis failed: ' . $e->getMessage());
        }
    }
}