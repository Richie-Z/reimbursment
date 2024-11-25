<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ResourceUpdatedNotification extends Notification
{
    use Queueable;

    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    // Define the notification channels (database in this case)
    public function via($notifiable)
    {
        return ['database'];
    }

    // Define the data to be saved in the database
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
