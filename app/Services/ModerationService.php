<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ModerationService
{
    public function moderate(string $text): array
    {

         // Block specific inappropriate words
    $blockedWords = ['dumb', 'stupid', 'idiot', 'moron', 'useless'];
    $lowerText = strtolower($text);
    
    foreach ($blockedWords as $word) {
        if (str_contains($lowerText, $word)) {
            return [
                'allowed' => false,
                'reason' => "Your announcement contains inappropriate language: '$word'",
                'raw' => null,
            ];
        }
    }


        // ============================================
        $baseUrl = rtrim(config('app.moderation_api_url', env('MODERATION_API_URL')), '/');

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

        return [
            'allowed' => $toxicityScore < 0.3,
            'reason' => $toxicityScore >= 0.3 ? 'Content flagged by moderation' : 'OK',
            'raw' => $data,
        ];
    }
}