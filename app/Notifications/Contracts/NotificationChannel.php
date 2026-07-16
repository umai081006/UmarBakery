<?php

namespace App\Notifications\Contracts;

use App\Notifications\DTO\NotificationPayload;

interface NotificationChannel
{
    /**
     * Send the given notification.
     *
     * @param NotificationPayload $payload
     * @return void
     */
    public function send(NotificationPayload $payload): void;
}
