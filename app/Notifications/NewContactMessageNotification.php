<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use App\Support\Brand;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $brand = Brand::data();

        return (new MailMessage)
            ->from(...Brand::fromAddress())
            ->subject('📩 New Contact Message: ' . $this->contactMessage->subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'Admin') . '!')
            ->line('You received a new message through the ' . $brand['name'] . ' contact form.')
            ->line('')
            ->line('**From:** ' . $this->contactMessage->name . ' (' . $this->contactMessage->email . ')')
            ->line('**Subject:** ' . $this->contactMessage->subject)
            ->line('**Message:**')
            ->line($this->contactMessage->message)
            ->action('View in Admin Panel', route('admin.messages.index'))
            ->line('Reply directly to the sender at ' . $this->contactMessage->email . '.');
    }

    public function toArray($notifiable): array
    {
        return [
            'contact_message_id' => $this->contactMessage->id,
            'name' => $this->contactMessage->name,
            'email' => $this->contactMessage->email,
            'subject' => $this->contactMessage->subject,
            'preview' => \Illuminate\Support\Str::limit($this->contactMessage->message, 80),
            'url' => route('admin.messages.index'),
        ];
    }
}
