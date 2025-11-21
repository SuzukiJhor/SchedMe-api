<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        $events = Auth::user()->events()->get();
        $events = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'full_name' => $event->full_name,
                'whatsapp' => $event->whatsapp,
                'notes' => $event->notes,
                'all_day' => $event->all_day,
                'start_time' => Carbon::parse($event->start_time)->format('Y/m/d'),
                'end_time' => Carbon::parse($event->end_time)->format('Y/m/d'),
            ];
        });

        return $events;
    }

    public function store(Request $request)
    {
        // Criar o validador manualmente
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'all_day' => 'boolean',
            'notes' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
        ], [
            'full_name.required' => 'O campo Nome é obrigatório.',
            'whatsapp.required' => 'O campo WhatsApp é obrigatório.',
            'start_time.date' => 'A data de início precisa estar no formato correto.',
            'end_time.date' => 'A data de término precisa estar no formato correto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['start_time'])) {
            $validated['start_time'] = Carbon::createFromFormat('d/m/Y', $validated['start_time'])->format('Y-m-d');
        }

        if (isset($validated['end_time'])) {
            $validated['end_time'] = Carbon::createFromFormat('d/m/Y', $validated['end_time'])->format('Y-m-d');
        }

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
