<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class Controller
{
    /** Build a unique slug for $modelClass from $value (or the title). */
    protected function uniqueSlug(string $modelClass, ?string $value, string $fallback, ?Model $ignore = null): string
    {
        $base = Str::slug($value ?: $fallback) ?: Str::random(6);
        $slug = $base;
        $i = 2;

        while ($modelClass::where('slug', $slug)->when($ignore, fn ($q) => $q->whereKeyNot($ignore->getKey()))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
