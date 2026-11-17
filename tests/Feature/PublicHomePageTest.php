<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_home_page_renders(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Quality products and fast repairs');
    }

    public function test_home_page_shows_active_announcements_only(): void
    {
        Announcement::factory()->create(['title' => 'Visible announcement', 'is_active' => true]);
        Announcement::factory()->create(['title' => 'Hidden announcement', 'is_active' => false]);

        $this->get('/')
            ->assertSee('Visible announcement')
            ->assertDontSee('Hidden announcement');
    }

    public function test_home_page_shows_active_services_only(): void
    {
        $category = ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]);

        Service::create([
            'service_category_id' => $category->id,
            'name' => 'Visible service',
            'price' => 50,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'name' => 'Hidden service',
            'price' => 50,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertSee('Visible service')
            ->assertDontSee('Hidden service');
    }
}
