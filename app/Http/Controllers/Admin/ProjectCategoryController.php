<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $counts = Project::whereNotNull('category')->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category');

        return view('admin.categories.index', [
            'categories' => ProjectCategory::ordered()->get(),
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $name = trim($data['name']);

        if ($this->findByName($name)) {
            return back()->withErrors(['name' => "“{$name}” already exists."])->withInput();
        }

        ProjectCategory::create(['name' => $name, 'sort_order' => $data['sort_order'] ?? 0]);

        return back()->with('success', 'Category added.');
    }

    /**
     * Rename a category (its projects follow). Renaming it to another category's name
     * merges the two: projects move over and this one is removed.
     */
    public function update(Request $request, ProjectCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $name = trim($data['name']);
        $other = $this->findByName($name, $category->id);

        if ($other) {
            DB::transaction(function () use ($category, $other) {
                Project::where('category', $category->name)->update(['category' => $other->name]);
                $category->delete();
            });

            return back()->with('success', "Merged “{$category->name}” into “{$other->name}”.");
        }

        DB::transaction(function () use ($category, $name, $data) {
            Project::where('category', $category->name)->update(['category' => $name]);
            $category->update(['name' => $name, 'sort_order' => $data['sort_order'] ?? 0]);
        });

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ProjectCategory $category)
    {
        DB::transaction(function () use ($category) {
            Project::where('category', $category->name)->update(['category' => null]);
            $category->delete();
        });

        return back()->with('success', 'Category deleted. Its projects are now uncategorized.');
    }

    private function findByName(string $name, ?int $exceptId = null): ?ProjectCategory
    {
        return ProjectCategory::whereRaw('lower(name) = ?', [mb_strtolower($name)])
            ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
            ->first();
    }
}
