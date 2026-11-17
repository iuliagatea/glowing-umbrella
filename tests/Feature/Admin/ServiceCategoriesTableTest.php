<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ServiceCategories\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategories\Pages\ListServiceCategories;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceCategoriesTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_shows_service_category_records(): void
    {
        $categories = collect([
            ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]),
            ServiceCategory::create(['name' => 'Phones', 'slug' => 'phones', 'sort_order' => 2]),
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListServiceCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_name_column_is_searchable(): void
    {
        $matching = ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]);
        $other = ServiceCategory::create(['name' => 'Phones', 'slug' => 'phones', 'sort_order' => 2]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListServiceCategories::class)
            ->searchTable('Laptops')
            ->assertCanSeeTableRecords([$matching])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_create_page_can_create_a_service_category(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateServiceCategory::class)
            ->fillForm(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('service_categories', ['name' => 'Laptops', 'slug' => 'laptops']);
    }
}
