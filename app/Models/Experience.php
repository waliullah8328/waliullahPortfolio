<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'type', 'title', 'organization', 'location', 'address', 'start_date', 'end_date',
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

    /**
     * Total experience across entries in whole months. Overlapping periods are counted once,
     * and both the start and end month count (Dec 2024 - Apr 2026 = 17 months).
     */
    public static function totalMonths(iterable $entries): int
    {
        $ranges = collect($entries)
            ->filter(fn (self $e) => $e->start_date)
            ->map(function (self $e) {
                $end = $e->is_current || ! $e->end_date ? now() : $e->end_date;
                $from = $e->start_date->year * 12 + $e->start_date->month;
                $to = $end->year * 12 + $end->month;

                return [$from, max($from, $to)];
            })
            ->sortBy(0)
            ->values();

        $total = 0;
        $cursor = null; // last month already counted
        foreach ($ranges as [$from, $to]) {
            $from = $cursor === null ? $from : max($from, $cursor + 1);
            if ($to >= $from) {
                $total += $to - $from + 1;
                $cursor = $to;
            }
        }

        return $total;
    }

    /** 1 -> "1 month", 14 -> "1 year 2 months", 24 -> "2 years". */
    public static function humanMonths(int $months): string
    {
        $y = intdiv($months, 12);
        $m = $months % 12;
        $parts = array_filter([
            $y ? $y.' year'.($y > 1 ? 's' : '') : null,
            $m ? $m.' month'.($m > 1 ? 's' : '') : null,
        ]);

        return implode(' ', $parts) ?: '1 month';
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
