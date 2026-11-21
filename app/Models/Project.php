<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'type', 'summary', 'description', 'image', 'tech_stack',
        'live_url', 'repo_url', 'app_store_url', 'play_store_url', 'is_featured', 'is_published', 'sort_order',
    ];

    public const TYPES = [
        'website' => 'Website',
        'app' => 'Mobile App',
        'both' => 'Web + Mobile App',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Website';
    }

    public function getIsAppAttribute(): bool
    {
        return in_array($this->type, ['app', 'both'], true);
    }

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_published' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('is_featured')->orderBy('sort_order')->latest();
    }

    public function getTechListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->tech_stack))));
    }

    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image);
    }

    public function getHtmlAttribute(): string
    {
        return Str::markdown($this->description ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
