<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'type', 'title', 'organization', 'location', 'start_date', 'end_date',
        'is_current', 'description', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'is_current' => 'boolean'];
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderByDesc('is_current')->orderByDesc('start_date');
    }

    public function getPeriodAttribute(): string
    {
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? 'Present');

        return $this->start_date->format('M Y').' - '.$end;
    }

    /** Description lines become bullet points (leading "-" or "*" is stripped). */
    public function getPointsAttribute(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->description);

        return array_values(array_filter(array_map(fn ($l) => trim(ltrim(trim($l), '-*')), $lines)));
    }

    /** Up to two initials of the organization, used as a logo placeholder. */
    public function getInitialsAttribute(): string
    {
        $clean = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', (string) $this->organization);
        $words = preg_split('/[\s\-]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_values(array_filter($words, fn ($w) => ! in_array(mb_strtolower($w), ['of', 'the', 'and'], true)));

        return mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2)))) ?: '•';
    }
}
