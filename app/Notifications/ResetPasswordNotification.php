<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public ?string $url = null;

    protected function resetUrl($notifiable): string
    {
        if ($this->url) {
            return $this->url;
        }

        return url(route('filament.admin.auth.password-reset.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Your Magna Credit Password')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $this->resetUrl($notifiable))
            ->line('This password reset link will expire in 3 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
