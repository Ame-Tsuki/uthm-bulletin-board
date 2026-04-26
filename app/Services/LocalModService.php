<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocalModService
{
    public function analyzeText(string $text, array $classifiers = ['toxicity', 'pii', 'spam']): array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post('http://127.0.0.1:8002/analyze', [
                    'text' => $text,
                    'classifiers' => $classifiers,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Debug log to see what's coming back
                \Log::info('LocalMod raw response', ['result' => $result]);
                
                // The API returns fields like: flagged, severity, results, processing_time_ms
                // Calculate a simple confidence score if not provided
                $confidence = 0;
                if (isset($result['results']) && is_array($result['results'])) {
                    // Get highest confidence from violations
                    foreach ($result['results'] as $violation) {
                        if (($violation['flagged'] ?? false) && isset($violation['confidence'])) {
                            $confidence = max($confidence, $violation['confidence']);
                        }
                    }
                }
                
                return [
                    'flagged' => $result['flagged'] ?? false,
                    'severity' => $result['severity'] ?? 'none',
                    'confidence' => $confidence,
                    'results' => $result['results'] ?? [],
                    'processing_time_ms' => $result['processing_time_ms'] ?? 0
                ];
            }

            return [
                'flagged' => false,
                'severity' => 'none',
                'confidence' => 0,
                'results' => [],
                'processing_time_ms' => 0,
                'error' => 'Moderation service error'
            ];

        } catch (\Exception $e) {
            Log::error('LocalMod connection failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'flagged' => false,
                'severity' => 'none',
                'confidence' => 0,
                'results' => [],
                'processing_time_ms' => 0,
                'error' => 'Could not connect to moderation service'
            ];
        }
    }
}