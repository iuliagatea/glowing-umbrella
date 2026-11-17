<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ServiceSeeder::class);
        $this->call(AnnouncementSeeder::class);

        // Placeholder site settings. firstOrCreate means values the owner already
        // edited in the admin panel are never overwritten when the seeder is re-run.
        $settings = [
            'site_name' => config('app.name'),
            'phone' => '+40 700 000 000',
            'email' => 'office@email.com',
            'address' => 'Main Street 123, City, Country',
            'opening_hours' => "Mon–Fri: 09:00–15:00\nSat-Sun: closed",
            'about_text' => "Write the company story here.\n\nA second paragraph about who you are and what you do.",
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // The admin account is NOT seeded on purpose (no default password in code).
        // Create it with: php artisan make:filament-user
    }
}
