<?php

namespace Tests\Feature\Models;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_relation_is_ordered_by_sort_order(): void
    {
        $category = ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]);

        $second = Service::create([
            'service_category_id' => $category->id,
            'name' => 'Second',
            'price' => 10,
            'sort_order' => 2,
        ]);

        $first = Service::create([
            'service_category_id' => $category->id,
            'name' => 'First',
            'price' => 10,
            'sort_order' => 1,
        ]);

        $this->assertSame([$first->id, $second->id], $category->services->pluck('id')->all());
    }
}
