<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementQuestion;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\AnnouncementNotification;

class AnnouncementQuestionController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Store a new question for an announcement.
     */
    public function store(Request $request, $announcementId)
    {
        $announcement = Announcement::findOrFail($announcementId);

        $request->validate([
            'question_text' => 'required|string|max:1000',
        ]);

        $questionText = $request->input('question_text');

        // Moderation Check
        $moderationResult = $this->moderationService->moderate($questionText, auth()->id());
        if (!$moderationResult['allowed']) {
            $errorMessage = "Your question was blocked by our content moderation system. " .
                           "Please remove inappropriate language. " .
                           "Reason: {$moderationResult['reason']}";
            
            return back()->withErrors(['question_text' => $errorMessage])->withInput();
        }

        $question = AnnouncementQuestion::create([
            'announcement_id' => $announcement->id,
            'user_id' => auth()->id(),
            'question_text' => $questionText,
        ]);

        // Send a notification to the announcement creator
        $creator = $announcement->author;
        if ($creator && $creator->id !== auth()->id()) {
            $title = "❓ New FAQ Question";
            $message = "Someone asked a question on your announcement '" . $announcement->title . "'";
            $url = route('announcements.show', $announcement->id) . '#questions-section';
            
            $creator->notify(new AnnouncementNotification($title, $message, $url, $announcement->id));
        }

        return back()->with('success', 'Your question has been submitted successfully! It will be listed in the FAQ once answered by the creator.');
    }

    /**
     * Answer a question.
     */
    public function answer(Request $request, $questionId)
    {
        $question = AnnouncementQuestion::findOrFail($questionId);
        $announcement = $question->announcement;
        $user = auth()->user();

        // Check permission: only announcement creator or admin/staff can answer
        if ($announcement->author_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized to answer this question.');
        }

        $request->validate([
            'answer_text' => 'required|string|max:2000',
        ]);

        $answerText = $request->input('answer_text');

        // Moderation Check
        $moderationResult = $this->moderationService->moderate($answerText, auth()->id());
        if (!$moderationResult['allowed']) {
            $errorMessage = "Your answer was blocked by our content moderation system. " .
                           "Please remove inappropriate language. " .
                           "Reason: {$moderationResult['reason']}";
            
            return back()->withErrors(['answer_text_' . $question->id => $errorMessage])->withInput();
        }

        $question->update([
            'answer_text' => $answerText,
            'answered_by' => $user->id,
            'answered_at' => now(),
        ]);

        // Notify the asker
        $asker = $question->asker;
        if ($asker && $asker->id !== $user->id) {
            $title = "💡 Question Answered";
            $message = "Your question on '" . $announcement->title . "' has been answered.";
            $url = route('announcements.show', $announcement->id) . '#question-' . $question->id;
            
            $asker->notify(new AnnouncementNotification($title, $message, $url, $announcement->id));
        }

        return back()->with('success', 'Question answered successfully!');
    }

    /**
     * Delete a question.
     */
    public function destroy($questionId)
    {
        $question = AnnouncementQuestion::findOrFail($questionId);
        $announcement = $question->announcement;
        $user = auth()->user();

        // Check permission: asker, announcement creator, or admin/staff
        if ($question->user_id !== $user->id && 
            $announcement->author_id !== $user->id && 
            !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized to delete this question.');
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }
}
