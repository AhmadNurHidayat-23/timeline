<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimelineRequest;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimelineController extends Controller
{
    public function index()
    {
        return view('timelines.index', ['timelines' => Timeline::withCount('events')
            ->orderByDesc('updated_at')->orderByDesc('id')->paginate(12)]);
    }

    public function create()
    {
        return view('timelines.form', ['timeline' => new Timeline]);
    }

    public function store(TimelineRequest $request)
    {
        $timeline = Timeline::create($request->validated());

        return to_route('timelines.show', $timeline)->with('status', 'Timeline berhasil dibuat. Tambahkan peristiwa pertama Anda.');
    }

    public function show(Timeline $timeline)
    {
        return view('timelines.show', ['timeline' => $timeline,
            'events' => $timeline->orderedEvents()->get(), 'readOnly' => false]);
    }

    public function edit(Timeline $timeline)
    {
        return view('timelines.form', compact('timeline'));
    }

    public function update(TimelineRequest $request, Timeline $timeline)
    {
        $timeline->update($request->validated());

        return to_route('timelines.show', $timeline)->with('status', 'Timeline berhasil diperbarui.');
    }

    public function destroy(Timeline $timeline)
    {
        $timeline->delete();

        return to_route('timelines.index')->with('status', 'Timeline dan seluruh peristiwanya telah dihapus.');
    }

    public function sort(Request $request, Timeline $timeline)
    {
        $data = $request->validate(['sort_mode' => ['required', Rule::in(['chronological', 'manual'])]]);
        $timeline->update($data);

        return to_route('timelines.show', $timeline)->with('status', 'Mode pengurutan diperbarui.');
    }

    public function shared(string $token)
    {
        $timeline = Timeline::where('share_token', $token)->firstOrFail();

        return view('timelines.show', ['timeline' => $timeline,
            'events' => $timeline->orderedEvents()->get(), 'readOnly' => true]);
    }
}
