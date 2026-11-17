<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_shows_setting_records(): void
    {
        $settings = collect([
            Setting::create(['key' => 'phone', 'value' => '0712 345 678']),
            Setting::create(['key' => 'email', 'value' => 'hello@example.com']),
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListSettings::class)
            ->assertOk()
            ->assertCanSeeTableRecords($settings);
    }

    public function test_key_column_is_searchable(): void
    {
        $matching = Setting::create(['key' => 'phone', 'value' => '0712 345 678']);
        $other = Setting::create(['key' => 'email', 'value' => 'hello@example.com']);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListSettings::class)
            ->searchTable('phone')
            ->assertCanSeeTableRecords([$matching])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_create_page_can_create_a_setting(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateSetting::class)
            ->fillForm(['key' => 'phone', 'value' => '0712 345 678'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', ['key' => 'phone', 'value' => '0712 345 678']);
    }
}
