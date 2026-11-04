<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Timeline;
use App\Services\EventOrdering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function store(EventRequest $request, Timeline $timeline)
    {
        DB::transaction(function () use ($request, $timeline) {
            $locked = Timeline::query()->lockForUpdate()->findOrFail($timeline->id);
            $locked->events()->create($request->validated() + [
                'position' => ($locked->events()->max('position') ?? -1) + 1,
            ]);
        });

        return to_route('timelines.show', $timeline)->with('status', 'Peristiwa berhasil ditambahkan.');
    }

    public function edit(Timeline $timeline, Event $event)
    {
        return view('events.edit', compact('timeline', 'event'));
    }

    public function update(EventRequest $request, Timeline $timeline, Event $event)
    {
        $event->update($request->validated());

        return to_route('timelines.show', $timeline)->with('status', 'Peristiwa berhasil diperbarui.');
    }

    public function destroy(Timeline $timeline, Event $event)
    {
        $event->delete();
        $timeline->touch();

        return to_route('timelines.show', $timeline)->with('status', 'Peristiwa telah dihapus.');
    }

    public function reorder(Request $request, Timeline $timeline, EventOrdering $ordering)
    {
        $data = $request->validate(['ids' => ['present', 'array'],
            'ids.*' => ['required', 'integer', 'distinct']]);
        $ordering->save($timeline, $data['ids']);

        return response()->json(['message' => 'Urutan manual tersimpan.']);
    }
}
