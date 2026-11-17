<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactMessagesTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_shows_contact_message_records(): void
    {
        $messages = collect([
            $this->createMessage(['name' => 'Jane Doe']),
            $this->createMessage(['name' => 'John Smith']),
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListContactMessages::class)
            ->assertOk()
            ->assertCanSeeTableRecords($messages);
    }

    public function test_name_column_is_searchable(): void
    {
        $matching = $this->createMessage(['name' => 'Jane Doe']);
        $other = $this->createMessage(['name' => 'John Smith']);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListContactMessages::class)
            ->searchTable('Jane Doe')
            ->assertCanSeeTableRecords([$matching])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_edit_page_can_mark_a_message_as_read(): void
    {
        $message = $this->createMessage(['read_at' => null]);

        $this->actingAs(User::factory()->create());

        Livewire::test(EditContactMessage::class, ['record' => $message->getRouteKey()])
            ->assertOk()
            ->fillForm(['read_at' => now()])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull($message->fresh()->read_at);
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
