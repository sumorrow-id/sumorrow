<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  User  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('auth.verify_notification_subject'))
            ->greeting(__('auth.verify_notification_greeting', ['name' => $notifiable->username]))
            ->line(__('auth.verify_notification_intro'))
            ->action(__('auth.verify_notification_action'), $this->verificationUrl($notifiable))
            ->line(__('auth.verify_notification_expiry', ['count' => config('auth.verification.expire', 60)]))
            ->line(__('auth.verify_notification_ignore'))
            ->line(__('auth.verify_notification_thanks'));
    }
}
