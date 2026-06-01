<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification
{
    use Queueable;

    protected $report;

    // Pass the report object into the notification
    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // Changed from toDatabase to toArray to match your working code
    public function toArray($notifiable)
    {
        return [
            'title' => 'Content Moderation Alert',
            'message' => 'A user has reported content.',
            'reason' => $this->report->reason ?? 'Flagged for review',
            'url' => route('admin.moderation') // Make sure this route exists
        ];
    }
}