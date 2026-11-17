<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return cache()->remember("setting.$key", 3600, fn () =>
            static::where('key', $key)->value('value') ?? $default
        );
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => cache()->forget("setting.{$s->key}"));
    }
}
