<?php

namespace App\Services;

use App\Models\Timeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventOrdering
{
    public function save(Timeline $timeline, array $ids): void
    {
        DB::transaction(function () use ($timeline, $ids) {
            $timeline = Timeline::query()->lockForUpdate()->findOrFail($timeline->id);
            $existing = $timeline->events()->pluck('id')->all();
            $submitted = array_map('intval', $ids);
            $sorted = $submitted;
            sort($existing);
            sort($sorted);

            if ($existing !== $sorted) {
                throw ValidationException::withMessages([
                    'ids' => 'Daftar peristiwa berubah. Muat ulang halaman sebelum mengurutkan.',
                ]);
            }

            foreach ($submitted as $position => $id) {
                $timeline->events()->whereKey($id)->update(['position' => $position]);
            }

            $timeline->update(['sort_mode' => 'manual']);
            $timeline->touch();
        });
    }
}
