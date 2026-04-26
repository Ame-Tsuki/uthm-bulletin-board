<?php

namespace App\Http\Controllers;

use App\Services\LocalModService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function test(Request $request, LocalModService $localMod)
    {
        $text = $request->input('text', 'Test message');
        
        try {
            $result = $localMod->analyzeText($text);
            
            return response()->json([
                'success' => true,
                'text' => $text,
                'is_safe' => !($result['flagged'] ?? false),
                'flagged' => $result['flagged'] ?? false,
                'severity' => $result['severity'] ?? 'none',
                'confidence' => $result['confidence'] ?? 0,
                'violations' => $result['results'] ?? [],
                'processing_time_ms' => $result['processing_time_ms'] ?? 0
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