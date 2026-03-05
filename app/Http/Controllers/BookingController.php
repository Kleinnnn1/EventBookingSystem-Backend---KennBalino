<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // POST /api/tickets/{id}/bookings
    public function store(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check availability
        $bookedQuantity = Booking::where('ticket_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('quantity');

        $available = $ticket->quantity - $bookedQuantity;

        if ($request->quantity > $available) {
            return response()->json([
                'message'   => 'Not enough tickets available',
                'available' => $available,
            ], 422);
        }

        // Prevent double booking
        $exists = Booking::where('user_id', auth()->id())
            ->where('ticket_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You have already booked this ticket',
            ], 422);
        }

        $booking = Booking::create([
            'user_id'   => auth()->id(),
            'ticket_id' => $id,
            'quantity'  => $request->quantity,
            'status'    => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking,
        ], 201);
    }

    // GET /api/bookings
    public function index()
    {
        $bookings = Booking::with(['ticket.event', 'payment'])
            ->where('user_id', auth()->id())
            ->paginate(10);

        return response()->json($bookings);
    }

    // PUT /api/bookings/{id}/cancel
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking already cancelled'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking,
        ]);
    }
}
