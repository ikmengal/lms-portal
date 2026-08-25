<?php

namespace App\Notifications;

use App\Support\Brand;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $activationUrl,
        public bool $isResend = false,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $brand = Brand::data();

        return (new MailMessage)
            ->from(...Brand::fromAddress())
            ->subject($this->isResend
                ? 'Activate Your ' . $brand['name'] . ' Account'
                : 'Welcome to ' . $brand['name'] . ' — Activate Your Account')
            ->view('emails.welcome', [
                'brand' => $brand,
                'user' => $notifiable,
                'activationUrl' => $this->activationUrl,
                'minutes' => '60 minutes',
                'isResend' => $this->isResend,
            ]);
    }
}
