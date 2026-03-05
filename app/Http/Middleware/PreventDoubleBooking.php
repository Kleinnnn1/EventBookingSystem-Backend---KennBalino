<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Booking;
use Illuminate\Http\Request;

class PreventDoubleBooking
{
    public function handle(Request $request, Closure $next): mixed
    {
        $ticketId = $request->route('id');

        $exists = Booking::where('user_id', $request->user()?->id)
            ->where('ticket_id', $ticketId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You have already booked this ticket',
            ], 422);
        }

        return $next($request);
    }
}
