<?php

namespace App\Http\Controllers;

use App\Services\LocalModService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function test(Request $request, \App\Services\ModerationService $moderationService)
    {
        $text = $request->input('text', 'Test message');
        
        try {
            $result = $moderationService->moderate($text, auth()->id());
            
            $flagged = !$result['allowed'];
            $violations = [];
            
            if ($flagged) {
                if (isset($result['reason'])) {
                    $violations[] = [
                        'classifier' => 'inappropriate language',
                        'flagged' => true,
                        'confidence' => 1.0,
                        'reason' => $result['reason']
                    ];
                }
                
                if (isset($result['raw']['matchedRuleIds'])) {
                    foreach ($result['raw']['matchedRuleIds'] as $ruleId) {
                        $violations[] = [
                            'classifier' => $ruleId,
                            'flagged' => true,
                            'confidence' => (float)($result['raw']['toxicityScore'] ?? 1.0)
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'text' => $text,
                'is_safe' => $result['allowed'],
                'flagged' => $flagged,
                'severity' => $flagged ? 'high' : 'none',
                'confidence' => $flagged ? 1.0 : 0,
                'violations' => $violations,
                'processing_time_ms' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, LocalModService $localMod)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $report = $localMod->analyzeText(
            $validated['body'],
            ['toxicity', 'pii', 'spam']
        );

        // Check if flagged
        if (($report['flagged'] ?? false) === true) {
            return response()->json([
                'success' => false,
                'message' => 'Comment blocked by moderation policy.',
                'severity' => $report['severity'] ?? 'unknown',
                'violations' => array_filter($report['results'] ?? [], function($v) {
                    return $v['flagged'] ?? false;
                })
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment accepted',
            'moderation' => [
                'flagged' => false,
                'severity' => 'none',
                'processing_time_ms' => $report['processing_time_ms'] ?? 0
            ]
        ]);
    }
}