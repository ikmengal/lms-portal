<?php

namespace App\Notifications;

use App\Support\Brand;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Dual-channel LMS notification: sends both in-app (database) and email.
 * Used by the Notifier service for all LMS events.
 */
class LmsNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public string $body = '',
        public ?string $url = null,
        public array $extra = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            ...$this->extra,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $brand = Brand::data();

        return (new MailMessage)
            ->from(...Brand::fromAddress())
            ->subject($this->title)
            ->view('emails.lms-notification', [
                'brand' => $brand,
                'user' => $notifiable,
                'title' => $this->title,
                'body' => $this->body,
                'url' => $this->url,
                'type' => $this->type,
                'extra' => $this->extra,
            ]);
    }
}
