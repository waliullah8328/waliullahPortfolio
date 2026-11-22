<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A project category managed in the admin. Projects store the category *name* in
 * projects.category, so renaming a category also renames it on its projects.
 */
class ProjectCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    public function projects()
    {
        return Project::where('category', $this->name);
    }
}
