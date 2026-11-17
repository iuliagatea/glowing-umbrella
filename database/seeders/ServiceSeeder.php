<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Placeholder data: replace names and prices with the company's real ones
     * (or just edit them later in the admin panel).
     *
     * Service format: [name, description, price, price_note]
     */
    public function run(): void
    {
        $catalog = [
            'Laptops & computers' => [
                ['Home visit and diagnosis', 'A technician comes to you and finds the fault.', 80.00, null],
                ['Diagnostic', 'Full hardware and software check. Free if you go ahead with the repair.', 50.00, null],
                ['Operating system reinstall', 'Clean install with drivers and updates.', 100.00, null],
                ['SSD upgrade', 'Labour only, drive not included. Data migration available.', 80.00, null],
                ['Cooling system cleaning', 'Dust removal and new thermal paste.', 120.00, null],
                ['Virus and malware removal', 'Scan, cleanup and basic protection setup.', 90.00, null],
            ],
        ];

        $categoryOrder = 0;

        foreach ($catalog as $categoryName => $services) {
            $category = ServiceCategory::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'sort_order' => ++$categoryOrder * 10]
            );

            $serviceOrder = 0;

            foreach ($services as [$name, $description, $price, $note]) {
                // updateOrCreate keeps the seeder safe to re-run without duplicating rows.
                // Note: re-running resets prices to the values below.
                Service::updateOrCreate(
                    ['service_category_id' => $category->id, 'name' => $name],
                    [
                        'description' => $description,
                        'price'       => $price,
                        'price_note'  => $note,
                        'is_active'   => true,
                        'sort_order'  => ++$serviceOrder * 10,
                    ]
                );
            }
        }
    }
}