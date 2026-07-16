<?php

namespace App\Notifications\Services;

use App\Notifications\DTO\NotificationPayload;
use App\Jobs\SendNotificationJob;

class NotificationService
{
    /**
     * Dispatch notification payload to the queue.
     *
     * @param NotificationPayload $payload
     * @return void
     */
    public function dispatch(NotificationPayload $payload): void
    {
        SendNotificationJob::dispatch($payload);
    }

    /**
     * Helper to send order-related notification.
     */
    public function sendOrderNotification(int $userId, string $title, string $message, string $type = 'order', array $metadata = []): void
    {
        $payload = new NotificationPayload(
            $userId,
            $title,
            $message,
            $type,
            ['database', 'email'],
            $metadata
        );

        $this->dispatch($payload);
    }
}
