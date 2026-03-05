<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // ← must be this

class EventTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsOrganizer()
    {
        $user  = User::factory()->create(['role' => 'organizer']);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [$user, $token];
    }

    public function test_organizer_can_create_event()
    {
        [$user, $token] = $this->actingAsOrganizer();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/events', [
                'title'       => 'Tech Conference',
                'description' => 'A tech event',
                'date'        => '2026-06-01 09:00:00',
                'location'    => 'Manila',
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Tech Conference']);
    }

    public function test_customer_cannot_create_event()
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/events', [
                'title'    => 'Tech Conference',
                'date'     => '2026-06-01 09:00:00',
                'location' => 'Manila',
            ]);

        $response->assertStatus(403);
    }

    public function test_anyone_can_view_events()
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        Event::factory(3)->create(['created_by' => $organizer->id]);

        $response = $this->getJson('/api/events');

        $response->assertStatus(200);
    }

    public function test_events_can_be_searched_by_title()
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        Event::factory()->create(['title' => 'Music Festival', 'created_by' => $organizer->id]);
        Event::factory()->create(['title' => 'Tech Summit',    'created_by' => $organizer->id]);

        $response = $this->getJson('/api/events?title=Music');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Music Festival']);
    }
}
