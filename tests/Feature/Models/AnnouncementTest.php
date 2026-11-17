<?php

namespace Tests\Feature\Models;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_includes_announcements_published_in_the_past(): void
    {
        $announcement = Announcement::factory()->create([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->assertTrue(Announcement::active()->whereKey($announcement->id)->exists());
    }

    public function test_active_scope_excludes_announcements_scheduled_in_the_future(): void
    {
        $announcement = Announcement::factory()->create([
            'is_active' => true,
            'published_at' => now()->addDay(),
        ]);

        $this->assertFalse(Announcement::active()->whereKey($announcement->id)->exists());
    }
}
