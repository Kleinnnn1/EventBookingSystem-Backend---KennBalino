<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // ← must be this

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_make_payment()
    {
        // Bind mock PaymentService into container
        app()->bind(PaymentService::class, function () {
            $mock = \Mockery::mock(PaymentService::class);
            $mock->shouldReceive('process')
                ->andReturn([
                    'status'  => 'success',
                    'amount'  => 100.00,
                    'message' => 'Payment processed successfully',
                ]);
            return $mock;
        });

        $organizer = User::factory()->create(['role' => 'organizer']);
        $event     = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket    = Ticket::factory()->create(['event_id' => $event->id, 'price' => 100]);
        $customer  = User::factory()->create(['role' => 'customer']);
        $token     = $customer->createToken('auth_token')->plainTextToken;

        $booking = Booking::factory()->create([
            'user_id'   => $customer->id,
            'ticket_id' => $ticket->id,
            'quantity'  => 1,
            'status'    => 'pending',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/bookings/{$booking->id}/payment");

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'success']);
    }

    public function test_cannot_pay_for_cancelled_booking()
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event     = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket    = Ticket::factory()->create(['event_id' => $event->id]);
        $customer  = User::factory()->create(['role' => 'customer']);
        $token     = $customer->createToken('auth_token')->plainTextToken;

        $booking = Booking::factory()->create([
            'user_id'   => $customer->id,
            'ticket_id' => $ticket->id,
            'status'    => 'cancelled',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/bookings/{$booking->id}/payment");

        $response->assertStatus(422);
    }
}
