<?php

namespace App\Notifications;

use App\Support\Brand;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $brand = Brand::data();

        return (new MailMessage)
            ->from(...Brand::fromAddress())
            ->subject('Reset Your ' . $brand['name'] . ' Password')
            ->view('emails.password-reset', [
                'brand' => $brand,
                'url' => $this->resetUrl($notifiable),
                'userName' => $notifiable->name,
                'minutes' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]);
    }
}
