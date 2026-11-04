<?php

namespace Tests\Feature;

use App\Models\Timeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_collection_and_create_form(): void
    {
        $this->get('/')->assertOk()->assertSee('Cerita dalam Garis Waktu.')
            ->assertSee('Buat timeline pertama');
        $this->get('/timelines/create')->assertOk()->assertSee('Beri perjalanan Anda nama.');
    }

    public function test_timeline_crud_persists_and_cascades_events(): void
    {
        $this->post('/timelines', ['name' => 'Perjalanan studio', 'description' => 'Dari sebuah ide.'])
            ->assertRedirect();
        $timeline = Timeline::firstOrFail();
        $this->assertSame(64, strlen($timeline->share_token));
        $this->assertSame('chronological', $timeline->sort_mode);
        $this->get('/')->assertSee('Perjalanan studio')->assertSee('0 peristiwa');
        $this->get("/timelines/{$timeline->id}/edit")->assertOk()->assertSee('Dari sebuah ide.');
        $this->put("/timelines/{$timeline->id}", ['name' => 'Studio berkembang', 'description' => 'Babak baru.'])
            ->assertRedirect("/timelines/{$timeline->id}");
        $this->get("/timelines/{$timeline->id}")->assertOk()->assertSee('Studio berkembang');
        $this->get("/timelines/{$timeline->id}")->assertSee('Babak baru.');
        $timeline->events()->create(['title' => 'Mulai', 'event_date' => '2026-01-01', 'position' => 0]);
        $this->delete("/timelines/{$timeline->id}")->assertRedirect('/');
        $this->assertDatabaseCount('timelines', 0);
        $this->assertDatabaseCount('events', 0);
        $this->get("/timelines/{$timeline->id}")->assertNotFound();
    }

    public function test_timeline_validation_rejects_invalid_names_and_long_descriptions(): void
    {
        foreach (['', '   ', str_repeat('a', 121)] as $name) {
            $this->post('/timelines', ['name' => $name])->assertSessionHasErrors('name');
        }
        $this->post('/timelines', ['name' => 'Test', 'description' => str_repeat('a', 501)])
            ->assertSessionHasErrors('description');
        $this->assertDatabaseCount('timelines', 0);
    }

    public function test_text_is_escaped_and_token_cannot_be_chosen_by_client(): void
    {
        $this->post('/timelines', ['name' => '<script>alert(1)</script>', 'share_token' => 'chosen']);
        $timeline = Timeline::firstOrFail();
        $this->assertNotSame('chosen', $timeline->share_token);
        $this->get("/timelines/{$timeline->id}")->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_collection_paginates_without_losing_items(): void
    {
        foreach (range(1, 13) as $number) Timeline::create(['name' => "Cerita {$number}"]);
        $this->get('/')->assertOk()->assertSee('Berikutnya');
        $this->get('/?page=2')->assertOk()->assertSee('Cerita 1')->assertSee('Sebelumnya');
    }
}
