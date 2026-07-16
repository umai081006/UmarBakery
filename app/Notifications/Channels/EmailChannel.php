<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Notifications\Contracts\NotificationChannel;
use App\Notifications\DTO\NotificationPayload;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;

class EmailChannel implements NotificationChannel
{
    public function send(NotificationPayload $payload): void
    {
        $user = User::find($payload->userId);
        if ($user && $user->email) {
            Mail::to($user->email)->send(new GenericNotificationMail($payload));
        }
    }
}
