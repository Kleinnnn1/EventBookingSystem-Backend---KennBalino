<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    // GET /api/events
    public function index(Request $request)
    {
        if ($request->hasAny(['title', 'date', 'location'])) {
            $cacheKey = 'events_' . md5(json_encode($request->all()));
        } else {
            $cacheKey = 'events_list'; 
        }

        $events = Cache::remember($cacheKey, 60, function () use ($request) {
            return Event::with('tickets')
                ->searchByTitle($request->title)
                ->filterByDate($request->date)
                ->when($request->location, function ($query) use ($request) {
                    $query->where('location', 'like', '%' . $request->location . '%');
                })
                ->paginate(10);
        });

        return response()->json($events);
    }

    // GET /api/events/{id}
    public function show($id)
    {
        $event = Event::with('tickets')->find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    // POST /api/events
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
        ]);

        $event = Event::create([
            'title'       => $request->title,
            'description' => $request->description,
            'date'        => $request->date,
            'location'    => $request->location,
            'created_by'  => auth()->id(),
        ]);

        Cache::flush();

        return response()->json([
            'message' => 'Event created successfully',
            'event'   => $event,
        ], 201);
    }

    // PUT /api/events/{id}
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if (auth()->user()->role === 'organizer' && $event->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'sometimes|date',
            'location'    => 'sometimes|string|max:255',
        ]);

        $event->update($request->only(['title', 'description', 'date', 'location']));

        Cache::flush();

        return response()->json([
            'message' => 'Event updated successfully',
            'event'   => $event,
        ]);
    }

    // DELETE /api/events/{id}
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if (auth()->user()->role === 'organizer' && $event->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        Cache::flush();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
