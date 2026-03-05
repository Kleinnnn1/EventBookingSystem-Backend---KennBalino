<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private function setupTicket(): Ticket
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event     = Event::factory()->create(['created_by' => $organizer->id]);
        return Ticket::factory()->create([
            'event_id' => $event->id,
            'quantity' => 10,
            'price'    => 50.00,
        ]);
    }

    public function test_customer_can_book_ticket()
    {
        $ticket   = $this->setupTicket();
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer, 'sanctum') // ← add sanctum guard
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'pending']);
    }

    public function test_customer_cannot_double_book()
    {
        $ticket   = $this->setupTicket();
        $customer = User::factory()->create(['role' => 'customer']);

        Booking::factory()->create([
            'user_id'   => $customer->id,
            'ticket_id' => $ticket->id,
            'status'    => 'confirmed',
            'quantity'  => 1,
        ]);

        $response = $this->actingAs($customer, 'sanctum') // ← add sanctum guard
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 1,
            ]);

        $response->assertStatus(422);
    }

    public function test_booking_fails_when_not_enough_tickets()
    {
        $ticket   = $this->setupTicket();
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer, 'sanctum') // ← add sanctum guard
            ->postJson("/api/tickets/{$ticket->id}/bookings", [
                'quantity' => 999,
            ]);

        $response->assertStatus(422);
    }

    public function test_customer_can_cancel_booking()
    {
        $ticket   = $this->setupTicket();
        $customer = User::factory()->create(['role' => 'customer']);

        $booking = Booking::factory()->create([
            'user_id'   => $customer->id,
            'ticket_id' => $ticket->id,
            'status'    => 'pending',
            'quantity'  => 1,
        ]);

        $response = $this->actingAs($customer, 'sanctum') // ← add sanctum guard
            ->putJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'cancelled']);
    }
}
