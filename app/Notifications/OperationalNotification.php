<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OperationalNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $url;
    protected $severity;
    protected $audience;

    public function __construct($title, $message, $url = '#', $severity = 'info', $audience = 'all')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->severity = $severity;
        $this->audience = $audience;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'operational',
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'severity' => $this->severity,
            'audience' => $this->audience,
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
