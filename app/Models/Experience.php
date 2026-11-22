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
    /** Whole months from start to end (or today when current), counting both end months. */
    public function getMonthsAttribute(): int
    {
        if (! $this->start_date) {
            return 0;
        }
        $end = $this->is_current || ! $this->end_date ? now() : $this->end_date;

        return max(0, (int) $this->start_date->copy()->startOfMonth()->diffInMonths($end->copy()->startOfMonth()) + 1);
    }

    /** 1 -> "1 mo", 14 -> "1 yr 2 mos", 24 -> "2 yrs". */
    public static function humanMonths(int $months): string
    {
        $y = intdiv($months, 12);
        $m = $months % 12;
        $parts = array_filter([
            $y ? $y.' yr'.($y > 1 ? 's' : '') : null,
            $m ? $m.' mo'.($m > 1 ? 's' : '') : null,
        ]);

        return implode(' ', $parts) ?: '1 mo';
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
