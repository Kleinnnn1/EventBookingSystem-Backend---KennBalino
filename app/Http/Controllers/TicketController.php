<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // POST /api/events/{event_id}/tickets
    public function store(Request $request, $event_id)
    {
        $event = Event::find($event_id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $request->validate([
            'type'     => 'required|string|max:100',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = Ticket::create([
            'event_id' => $event_id,
            'type'     => $request->type,
            'price'    => $request->price,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket'  => $ticket,
        ], 201);
    }

    // PUT /api/tickets/{id}
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $request->validate([
            'type'     => 'sometimes|string|max:100',
            'price'    => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $ticket->update($request->only(['type', 'price', 'quantity']));

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket'  => $ticket,
        ]);
    }

    // DELETE /api/tickets/{id}
    public function destroy($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
