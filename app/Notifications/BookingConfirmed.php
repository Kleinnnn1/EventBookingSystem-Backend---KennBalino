<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmed extends Notification implements ShouldQueue // ← async queue
{
    use Queueable;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    // Send via mail and database
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    // Email content
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed.')
            ->line('Ticket Type : ' . $this->booking->ticket->type)
            ->line('Quantity    : ' . $this->booking->quantity)
            ->line('Amount Paid : $' . $this->booking->payment->amount)
            ->action('View Booking', url('/api/bookings'))
            ->line('Thank you for your booking!');
    }

    // Database content
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id'  => $this->booking->id,
            'ticket_type' => $this->booking->ticket->type,
            'quantity'    => $this->booking->quantity,
            'message'     => 'Your booking has been confirmed!',
        ];
    }
}
