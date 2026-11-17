<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Placeholder data: replace with the company's real announcements
     * (or edit them later in the admin panel).
     */
    public function run(): void
    {
        $announcements = [
            ['New laptop repair bench open', 'We have added a second repair bench, so turnaround on laptop diagnostics is now faster.'],
            ['Extended Saturday hours', 'We are now open Saturdays 09:00-13:00 for drop-offs and collections.'],
            ['Spring clean-up discount', 'Book a cooling system cleaning this month and save 15% on labour.'],
        ];

        $order = 0;

        foreach ($announcements as [$title, $body]) {
            Announcement::updateOrCreate(
                ['title' => $title],
                [
                    'body' => $body,
                    'is_active' => true,
                    'published_at' => now(),
                    'sort_order' => ++$order * 10,
                ]
            );
        }
    }
}
