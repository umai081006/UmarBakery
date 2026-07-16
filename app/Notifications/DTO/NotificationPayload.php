<?php

namespace App\Notifications\DTO;

class NotificationPayload
{
    public int $userId;
    public string $title;
    public string $message;
    public string $type;
    public array $channels;
    public array $metadata;

    public function __construct(
        int $userId,
        string $title,
        string $message,
        string $type = 'system',
        array $channels = ['database'],
        array $metadata = []
    ) {
        $this->userId = $userId;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->channels = $channels;
        $this->metadata = $metadata;
    }
}
