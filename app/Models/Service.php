<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'tags', 'sort_order', 'is_visible'];

    /** Icons available in the admin picker (names from components/icon.blade.php). */
    public const ICONS = [
        'smartphone', 'layers', 'palette', 'zap', 'database', 'rocket', 'shield-check',
        'git-branch', 'code', 'monitor', 'sparkles', 'globe', 'android', 'apple',
    ];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean'];
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function getTagListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tags))));
    }
}
