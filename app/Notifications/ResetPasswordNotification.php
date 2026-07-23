<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// ponytail: sent synchronously (see VerifyEmailNotification) — same latent bug,
// no queue worker on the VM means a queued reset email would never send.
class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $token,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(
            '/reset-password/'.$this->token.'?email='.urlencode($notifiable->getEmailForPasswordReset())
        );

        return (new MailMessage)
            ->subject(__('auth.reset_notification_subject'))
            ->greeting(__('auth.reset_notification_greeting', ['name' => $notifiable->username]))
            ->line(__('auth.reset_notification_intro'))
            ->action(__('auth.reset_notification_action'), $resetUrl)
            ->line(__('auth.reset_notification_expiry'))
            ->line(__('auth.reset_notification_ignore'))
            ->line(__('auth.reset_notification_thanks'));
    }
}
