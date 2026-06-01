<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $url;

    // We pass the title, message, and target link dynamically
    public function __construct($title, $message, $url = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    // Set the channel to 'database'
    public function via($notifiable)
    {
        return ['database'];
    }

    // This data will be saved inside the 'data' column in your DB as JSON
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}