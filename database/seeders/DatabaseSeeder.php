<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 2 Admins
        $admins = User::factory(2)->create([
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 3 Organizers
        $organizers = User::factory(3)->create([
            'role'     => 'organizer',
            'password' => Hash::make('password'),
        ]);

        // 10 Customers
        $customers = User::factory(10)->create([
            'role'     => 'customer',
            'password' => Hash::make('password'),
        ]);

        // 5 Events (created by organizers)
        $events = collect();
        for ($i = 1; $i <= 5; $i++) {
            $events->push(Event::create([
                'title'      => "Event $i",
                'description' => "Description for event $i",
                'date'       => now()->addDays($i * 7),
                'location'   => "Location $i",
                'created_by' => $organizers->random()->id,
            ]));
        }

        // 15 Tickets (3 per event: VIP, Standard, Economy)
        $tickets = collect();
        foreach ($events as $event) {
            foreach (['VIP' => 500, 'Standard' => 200, 'Economy' => 100] as $type => $price) {
                $tickets->push(Ticket::create([
                    'event_id' => $event->id,
                    'type'     => $type,
                    'price'    => $price,
                    'quantity' => 50,
                ]));
            }
        }

        // 20 Bookings
        for ($i = 0; $i < 20; $i++) {
            Booking::create([
                'user_id'   => $customers->random()->id,
                'ticket_id' => $tickets->random()->id,
                'quantity'  => rand(1, 3),
                'status'    => collect(['pending', 'confirmed', 'cancelled'])->random(),
            ]);
        }
    }
}
