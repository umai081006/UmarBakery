<?php

namespace App\Notifications\Channels;

use App\Models\Notification;
use App\Notifications\Contracts\NotificationChannel;
use App\Notifications\DTO\NotificationPayload;

class DatabaseChannel implements NotificationChannel
{
    public function send(NotificationPayload $payload): void
    {
        Notification::create([
            'user_id' => $payload->userId,
            'type' => $payload->type,
            'title' => $payload->title,
            'message' => $payload->message,
            'data' => $payload->metadata,
        ]);
    }
}
