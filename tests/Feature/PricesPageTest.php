<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricesPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_prices_page_only_shows_active_services(): void
    {
        $category = ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]);

        Service::create([
            'service_category_id' => $category->id,
            'name' => 'Visible repair',
            'price' => 100,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'name' => 'Hidden repair',
            'price' => 100,
            'is_active' => false,
        ]);

        $this->get(route('prices'))
            ->assertSee('Visible repair')
            ->assertDontSee('Hidden repair');
    }

    public function test_prices_page_hides_categories_with_no_active_services(): void
    {
        ServiceCategory::create(['name' => 'Empty category', 'slug' => 'empty-category', 'sort_order' => 1]);

        $inactiveOnly = ServiceCategory::create(['name' => 'Inactive only', 'slug' => 'inactive-only', 'sort_order' => 2]);

        Service::create([
            'service_category_id' => $inactiveOnly->id,
            'name' => 'Inactive repair',
            'price' => 100,
            'is_active' => false,
        ]);

        $this->get(route('prices'))
            ->assertDontSee('Empty category')
            ->assertDontSee('Inactive only');
    }
}
