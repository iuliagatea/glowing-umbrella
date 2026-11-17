<?php

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'image', 'is_active', 'published_at', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean', 'published_at' => 'datetime'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('published_at', '<=', now());
    }
}
