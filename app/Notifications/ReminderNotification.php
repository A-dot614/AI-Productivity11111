<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Reminder $reminder) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->reminder->subject ?? 'Task reminder')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($this->reminder->message ?? 'You have an upcoming reminder.')
            ->when($this->reminder->remindable !== null, function (MailMessage $message) {
                $message->line('Related item: '.($this->reminder->remindable?->title ?? 'Your task'));
            })
            ->line('Reminder time: '.$this->reminder->remind_at->format('D, M j, Y g:i A'))
            ->action('Open '.config('app.name'), url('/dashboard'));
    }
}
