<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Notifications\DTO\NotificationPayload;
use App\Notifications\Channels\EmailChannel;
use App\Notifications\Channels\DatabaseChannel;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public NotificationPayload $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(NotificationPayload $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $channels = [
            'email' => new EmailChannel(),
            'database' => new DatabaseChannel(),
        ];

        foreach ($this->payload->channels as $channelName) {
            if (isset($channels[$channelName])) {
                $channels[$channelName]->send($this->payload);
            }
        }
    }
}
