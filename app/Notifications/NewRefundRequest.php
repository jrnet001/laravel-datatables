<?php

namespace App\Notifications;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewRefundRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Refund $refund)
    {
        //
        $this->refund = $refund;
    }

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
        error_log("To mail.");

        return (new MailMessage)
            //->line('The introduction to the notification.')

            ->subject("Refund request for {$this->refund->firstName} {$this->refund->lastName}")
            ->greeting("Refund request for {$this->refund->firstName} {$this->refund->lastName} {$this->refund->libraryCard} ")
            ->action('Refund Action', url(path: '/'))
            ->line("Processed by: {$this->refund->user->name}")
            ->line("Refund: $ {$this->refund->amount}")
            //->greeting("New Chirp from {$this->refund->user->name}")
            ->line(Str::limit($this->refund->notes, 50));

            //->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
