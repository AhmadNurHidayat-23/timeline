<?php

namespace Tests\Feature;

use App\Models\Timeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_link_is_read_only_and_works_for_remote_visitors(): void
    {
        $timeline = Timeline::create(['name' => 'Cerita publik']);
        $timeline->events()->create(['title' => 'Peristiwa publik', 'event_date' => '2026-01-01', 'position' => 0]);
        $url = "/s/{$timeline->share_token}";
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8']);
        $this->get($url)->assertOk()->assertSee('Peristiwa publik')
            ->assertSee('HANYA LIHAT')->assertDontSee('Tambah peristiwa')->assertDontSee('Edit timeline')
            ->assertDontSee('data-reorder-url')->assertDontSee('<form', false);
        $this->get($url)->assertOk()->assertSee('Cerita publik');
        $this->post($url, ['name' => 'Berubah'])->assertMethodNotAllowed();
        $this->put($url, ['name' => 'Berubah'])->assertMethodNotAllowed();
        $this->delete($url)->assertMethodNotAllowed();
        $this->assertSame('Cerita publik', $timeline->fresh()->name);
    }

    public function test_remote_management_is_blocked_even_with_forged_forwarding_header(): void
    {
        $timeline = Timeline::create(['name' => 'Privat']);
        $event = $timeline->events()->create(['title' => 'Privat', 'event_date' => '2026-01-01', 'position' => 0]);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8'])->withHeader('X-Forwarded-For', '127.0.0.1');
        $this->get('/')->assertForbidden();
        $this->get('/timelines/create')->assertForbidden();
        $this->get("/timelines/{$timeline->id}")->assertForbidden();
        $this->post('/timelines', ['name' => 'Intrusi'])->assertForbidden();
        $this->put("/timelines/{$timeline->id}", ['name' => 'Intrusi'])->assertForbidden();
        $this->delete("/timelines/{$timeline->id}")->assertForbidden();
        $this->post("/timelines/{$timeline->id}/events", [])->assertForbidden();
        $this->put("/timelines/{$timeline->id}/events/{$event->id}", [])->assertForbidden();
        $this->delete("/timelines/{$timeline->id}/events/{$event->id}")->assertForbidden();
        $this->putJson("/timelines/{$timeline->id}/order", ['ids' => [$event->id]])->assertForbidden();
        $this->patch("/timelines/{$timeline->id}/sort", ['sort_mode' => 'manual'])->assertForbidden();
        $this->assertDatabaseCount('timelines', 1);
        $this->assertDatabaseCount('events', 1);
    }

    public function test_unknown_share_tokens_and_deleted_timelines_return_not_found(): void
    {
        $a = Timeline::create(['name' => 'A']);
        $b = Timeline::create(['name' => 'B']);
        $this->assertNotSame($a->share_token, $b->share_token);
        $this->get('/s/'.str_repeat('a', 64))->assertNotFound();
        $this->get('/s/1')->assertNotFound();
        $token = $a->share_token;
        $a->delete();
        $this->get("/s/{$token}")->assertNotFound();
    }

    public function test_mutations_require_a_valid_csrf_token(): void
    {
        // Laravel bypasses request forgery checks in testing; enable the real check here.
        $this->app['env'] = 'local';
        $this->post('/timelines', ['name' => 'Tanpa token'])->assertStatus(419);
        $this->assertDatabaseCount('timelines', 0);
        $this->withSession(['_token' => 'valid-test-token'])
            ->post('/timelines', ['name' => 'Dengan token', '_token' => 'valid-test-token'])
            ->assertRedirect();
        $this->assertDatabaseCount('timelines', 1);
    }
}
