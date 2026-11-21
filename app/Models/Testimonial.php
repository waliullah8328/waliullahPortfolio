<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'position', 'company', 'avatar', 'content', 'rating', 'is_visible', 'sort_order'];

    protected function casts(): array
    {
        return ['is_visible' => 'boolean', 'rating' => 'integer'];
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->latest();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return Media::url($this->avatar);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    }
}
