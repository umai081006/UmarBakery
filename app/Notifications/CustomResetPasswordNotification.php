<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Akun Umar Bakery'))
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line(Lang::get('Kami menerima permintaan untuk mengatur ulang password akun Umar Bakery Anda.'))
            ->line(Lang::get('Jika Anda memang meminta perubahan password, klik tombol di bawah ini:'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('Link reset password ini akan kedaluwarsa dalam :count menit.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('Jika Anda tidak meminta perubahan password:'))
            ->line(Lang::get('- abaikan email ini'))
            ->line(Lang::get('- password Anda tidak berubah'))
            ->line(Lang::get('- jangan bagikan link reset kepada siapapun'));
    }
}
