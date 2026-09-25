<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calendar\MoveCalendarEventRequest;
use App\Http\Requests\Calendar\StoreCalendarEventRequest;
use App\Http\Requests\Calendar\UpdateCalendarEventRequest;
use App\Models\CalendarEvent;
use Illuminate\Http\RedirectResponse;

class CalendarEventController extends Controller
{
    public function store(StoreCalendarEventRequest $request): RedirectResponse
    {
        CalendarEvent::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'color' => $request->validated('color') ?? '#6366f1',
        ]);

        return redirect()
            ->route('calendar.index')
            ->with('toast', ['type' => 'success', 'title' => 'Event created', 'message' => 'Your event has been added to the calendar.']);
    }

    public function update(UpdateCalendarEventRequest $request, CalendarEvent $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()
            ->route('calendar.index')
            ->with('toast', ['type' => 'success', 'title' => 'Event updated', 'message' => 'Your event has been updated.']);
    }

    public function move(MoveCalendarEventRequest $request, CalendarEvent $event): RedirectResponse
    {
        $event->update($request->validated());

        return back()->with('toast', ['type' => 'success', 'title' => 'Event moved', 'message' => 'The event was moved.']);
    }

    public function destroy(CalendarEvent $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('calendar.index')
            ->with('toast', ['type' => 'success', 'title' => 'Event deleted', 'message' => 'The event has been removed.']);
    }
}
