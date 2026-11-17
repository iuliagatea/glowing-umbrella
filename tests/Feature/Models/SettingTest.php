<?php

namespace Tests\Feature\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_the_given_default_when_the_setting_is_missing(): void
    {
        $this->assertSame('fallback', Setting::get('missing_key', 'fallback'));
    }

    public function test_get_returns_the_stored_value(): void
    {
        Setting::create(['key' => 'phone', 'value' => '0712 345 678']);

        $this->assertSame('0712 345 678', Setting::get('phone'));
    }

    public function test_get_caches_the_value_until_the_setting_is_saved_again(): void
    {
        Setting::create(['key' => 'phone', 'value' => 'original']);
        Setting::get('phone');

        DB::table('settings')->where('key', 'phone')->update(['value' => 'changed directly in db']);

        $this->assertSame('original', Setting::get('phone'));
    }

    public function test_saving_a_setting_invalidates_its_cache(): void
    {
        $setting = Setting::create(['key' => 'phone', 'value' => 'original']);
        Setting::get('phone');

        $setting->update(['value' => 'updated']);

        $this->assertSame('updated', Setting::get('phone'));
    }
}
