<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\LatestContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LatestContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_shows_contact_message_records(): void
    {
        $messages = collect([
            $this->createMessage(['name' => 'Jane Doe']),
            $this->createMessage(['name' => 'John Smith']),
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(LatestContactMessages::class)
            ->assertOk()
            ->assertCanSeeTableRecords($messages);
    }

    public function test_widget_sorts_newest_message_first(): void
    {
        $older = $this->createMessage(['name' => 'Older', 'created_at' => now()->subDay()]);
        $newer = $this->createMessage(['name' => 'Newer', 'created_at' => now()]);

        $this->actingAs(User::factory()->create());

        Livewire::test(LatestContactMessages::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
    }

    private function createMessage(array $attributes = []): ContactMessage
    {
        return ContactMessage::create(array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0712 345 678',
            'message' => 'I need a quote for a laptop repair.',
        ], $attributes));
    }
}
