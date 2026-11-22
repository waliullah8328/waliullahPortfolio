<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'title', 'issuer', 'issued_at', 'expires_at', 'credential_id', 'credential_url',
        'image', 'skills', 'sort_order', 'is_visible',
    ];

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'expires_at' => 'date', 'is_visible' => 'boolean'];
    }

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_visible', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderByDesc('issued_at')->orderByDesc('id');
    }

    public function getSkillListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->skills))));
    }

    /** Card-sized preview (Google Drive links are resized on Google's side). */
    public function getImageUrlAttribute(): ?string
    {
        return Media::url($this->image, false, 1200);
    }

    /** Full-size version for the lightbox. */
    public function getImageFullUrlAttribute(): ?string
    {
        return Media::url($this->image, false, 2400);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /** Up to two initials of the issuer, used as a logo placeholder. */
    public function getInitialsAttribute(): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', (string) $this->issuer);
        $words = preg_split('/[\s\-]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_values(array_filter($words, fn ($w) => ! in_array(mb_strtolower($w), ['of', 'the', 'and'], true)));

        return mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2)))) ?: '•';
    }
}
