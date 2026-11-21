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
}
