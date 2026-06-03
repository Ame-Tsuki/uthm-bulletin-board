<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    // Cultural whitelist - these terms are always allowed
    protected $culturalWhitelist = [
        'gawai', 'hari gawai', 'dayak', 'sarawak',
        'harvest festival', 'pua kumbu', 'ngajat',
        'raya', 'deepavali', 'christmas', 'eid',
        'holiday', 'celebration', 'festival', 'cultural',
    ];

    // Blocked words - these are always blocked
    protected $blockedWords = [

	//English
	'dumb', 'stupid', 'idiot', 'moron', 'useless',
	//Malay
	'bodoh', 'gila', 'sial', 'teruk', 'biadab'
	];

    public function moderate(string $text, ?int $userId = null): array
    {
        $lowerText = strtolower($text);
        
        // If userId not provided, try to get from auth
        if ($userId === null && auth()->check()) {
            $userId = auth()->id();
        }

        // Check cultural whitelist first - always allow
        foreach ($this->culturalWhitelist as $term) {
            if (str_contains($lowerText, $term)) {
                return [
                    'allowed' => true,
                    'reason' => 'Cultural content allowed',
                    'raw' => null,
                ];
            }
        }

        // Check blocked words - always block
        foreach ($this->blockedWords as $word) {
            if (str_contains($lowerText, $word)) {
                // LOG THE BLOCKED CONTENT
                Log::warning('Content blocked by keyword filter', [
                    'user_id' => $userId,
                    'blocked_word' => $word,
                    'content_preview' => substr($text, 0, 200),
                    'full_content' => $text,
                    'reason' => "Contains blocked word: '$word'",
                    'ip' => request()->ip(),
                    'timestamp' => now()->toDateTimeString(),
                ]);
                
                return [
                    'allowed' => false,
                    'reason' => "Your announcement contains inappropriate language: '$word'",
                    'raw' => null,
                ];
            }
        }

        // Call the .NET API for more nuanced analysis
        $baseUrl = rtrim(config('app.moderation_api_url', env('MODERATION_API_URL')), '/');

        try {
            $response = Http::timeout(10)->post("{$baseUrl}/api/moderate", [
                'text' => $text,
            ]);

            if (!$response->ok()) {
                return [
                    'allowed' => true,
                    'reason' => 'Moderation API unavailable',
                    'raw' => null,
                ];
            }

            $data = $response->json();
            $toxicityScore = (float) ($data['toxicityScore'] ?? 0);
            $matchedRules = $data['matchedRuleIds'] ?? [];
            
            $isAllowed = $toxicityScore < 0.85;

            // LOG BLOCKED CONTENT FROM API
            if (!$isAllowed) {
                Log::warning('Content blocked by AI moderation', [
                    'user_id' => $userId,
                    'toxicity_score' => $toxicityScore,
                    'matched_rules' => $matchedRules,
                    'content_preview' => substr($text, 0, 200),
                    'full_content' => $text,
                    'reasoning' => $data['reasoning'] ?? 'No reasoning provided',
                    'ip' => request()->ip(),
                    'timestamp' => now()->toDateTimeString(),
                ]);
            } else {
                // Optional: Log borderline content for review (score between 0.6 and 0.85)
                if ($toxicityScore >= 0.6 && $toxicityScore < 0.85) {
                    Log::info('Borderline content allowed', [
                        'user_id' => $userId,
                        'toxicity_score' => $toxicityScore,
                        'matched_rules' => $matchedRules,
                        'content_preview' => substr($text, 0, 200),
                        'timestamp' => now()->toDateTimeString(),
                    ]);
                }
            }

            return [
                'allowed' => $isAllowed,
                'reason' => !$isAllowed ? 'Content flagged by moderation' : 'OK',
                'raw' => $data,
            ];
            
        } catch (\Exception $e) {
            Log::error('Moderation API error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'content_preview' => substr($text, 0, 200),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return [
                'allowed' => true,
                'reason' => 'Moderation service temporarily unavailable',
                'raw' => null,
            ];
        }
    }
}
