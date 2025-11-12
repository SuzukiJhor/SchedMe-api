<?php

namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        return Auth::user()->events()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'all_day' => 'boolean',
            'notes' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        $event = Auth::user()->events()->create($validated);
        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $event->update($request->validate([
            'full_name' => 'sometimes|string|max:255',
            'whatsapp' => 'sometimes|string|max:20',
            'all_day' => 'boolean',
            'notes' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]));

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return response()->noContent();
    }
}
