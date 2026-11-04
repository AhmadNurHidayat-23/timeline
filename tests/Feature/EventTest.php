<?php

namespace Tests\Feature;

use App\Models\Timeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    private function timeline(): Timeline
    {
        return Timeline::create(['name' => 'Sebuah perjalanan']);
    }

    public function test_event_crud_and_refresh_persistence(): void
    {
        $timeline = $this->timeline();
        $base = "/timelines/{$timeline->id}";
        $this->post("{$base}/events", ['title' => 'Bermula', 'event_date' => '2026-01-02', 'description' => 'Ide pertama.'])
            ->assertRedirect($base);
        $event = $timeline->events()->firstOrFail();
        $this->assertSame(0, $event->position);
        $this->get("{$base}/events/{$event->id}/edit")->assertOk()->assertSee('Bermula');
        $this->put("{$base}/events/{$event->id}", ['title' => 'Awal baru', 'event_date' => '2025-12-01', 'description' => 'Catatan diperbarui.'])
            ->assertRedirect($base);
        $this->get($base)->assertSee('Awal baru')->assertSee('01 Des 2025');
        $this->get($base)->assertSee('Catatan diperbarui.');
        $this->assertDatabaseHas('events', ['id' => $event->id, 'event_date' => '2025-12-01 00:00:00']);
        $this->delete("{$base}/events/{$event->id}")->assertRedirect($base);
        $this->assertDatabaseCount('events', 0);
    }

    public function test_event_validation_rejects_impossible_dates_and_long_text(): void
    {
        $timeline = $this->timeline();
        $url = "/timelines/{$timeline->id}/events";
        $this->post($url, [])->assertSessionHasErrors(['title', 'event_date']);
        foreach (['2026-02-30', 'yesterday', '0000-01-01', '10000-01-01'] as $date) {
            $this->post($url, ['title' => 'Test', 'event_date' => $date])->assertSessionHasErrors('event_date');
        }
        $this->post($url, ['title' => str_repeat('x', 161), 'event_date' => '2026-01-01',
            'description' => str_repeat('x', 1001)])->assertSessionHasErrors(['title', 'description']);
        $this->assertDatabaseCount('events', 0);
    }

    public function test_events_are_scoped_to_their_timeline(): void
    {
        $owner = $this->timeline();
        $other = $this->timeline();
        $event = $owner->events()->create(['title' => 'Milik pertama', 'event_date' => '2026-01-01', 'position' => 0]);
        $path = "/timelines/{$other->id}/events/{$event->id}";
        $this->get("{$path}/edit")->assertNotFound();
        $this->put($path, ['title' => 'Diubah', 'event_date' => '2026-01-02'])->assertNotFound();
        $this->delete($path)->assertNotFound();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Milik pertama']);
    }

    public function test_manual_order_survives_chronological_mode_and_date_edits(): void
    {
        $timeline = $this->timeline();
        $late = $timeline->events()->create(['title' => 'Paling akhir', 'event_date' => '2026-12-01', 'position' => 0]);
        $early = $timeline->events()->create(['title' => 'Paling awal', 'event_date' => '2026-01-01', 'position' => 1]);
        $base = "/timelines/{$timeline->id}";
        $this->get($base)->assertSeeInOrder(['Paling awal', 'Paling akhir']);
        $this->putJson("{$base}/order", ['ids' => [$late->id, $early->id]])->assertOk();
        $this->get($base)->assertSeeInOrder(['Paling akhir', 'Paling awal']);
        $this->patch("{$base}/sort", ['sort_mode' => 'chronological'])->assertRedirect();
        $this->get($base)->assertSeeInOrder(['Paling awal', 'Paling akhir']);
        $this->put("{$base}/events/{$late->id}", ['title' => 'Paling akhir', 'event_date' => '2026-06-01']);
        $this->patch("{$base}/sort", ['sort_mode' => 'manual']);
        $this->get($base)->assertSeeInOrder(['Paling akhir', 'Paling awal']);
        $this->get($base)->assertSeeInOrder(['Paling akhir', 'Paling awal']);
        $this->assertSame(0, $late->fresh()->position);
        $this->assertSame(1, $early->fresh()->position);
        $this->post("{$base}/events", ['title' => 'Tambahan', 'event_date' => '2025-01-01']);
        $this->assertSame(2, $timeline->events()->where('title', 'Tambahan')->value('position'));
    }

    public function test_same_date_has_deterministic_order_and_invalid_mode_is_rejected(): void
    {
        $timeline = $this->timeline();
        foreach (['Pertama', 'Kedua'] as $position => $title) {
            $timeline->events()->create(['title' => $title, 'event_date' => '2026-01-01', 'position' => $position]);
        }
        $this->get("/timelines/{$timeline->id}")->assertSeeInOrder(['Pertama', 'Kedua']);
        $this->patch("/timelines/{$timeline->id}/sort", ['sort_mode' => 'invalid'])->assertSessionHasErrors('sort_mode');
    }

    public function test_reorder_requires_exact_unique_set_of_owned_ids_and_is_atomic(): void
    {
        $timeline = $this->timeline();
        $other = $this->timeline();
        $a = $timeline->events()->create(['title' => 'A', 'event_date' => '2026-01-01', 'position' => 0]);
        $b = $timeline->events()->create(['title' => 'B', 'event_date' => '2026-01-02', 'position' => 1]);
        $foreign = $other->events()->create(['title' => 'C', 'event_date' => '2026-01-03', 'position' => 0]);
        foreach ([[], [$a->id], [$a->id, $a->id], [$a->id, $foreign->id], [$a->id, 9999], ['bad', $b->id]] as $ids) {
            $this->putJson("/timelines/{$timeline->id}/order", ['ids' => $ids])->assertUnprocessable();
            $this->assertSame(0, $a->fresh()->position);
            $this->assertSame(1, $b->fresh()->position);
        }
        $this->assertSame('chronological', $timeline->fresh()->sort_mode);
        $this->putJson("/timelines/{$timeline->id}/order", [])->assertUnprocessable();
    }
}
