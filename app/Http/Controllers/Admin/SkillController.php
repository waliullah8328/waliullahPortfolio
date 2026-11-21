<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        return view('admin.skills.index', ['skills' => Skill::ordered()->get()->groupBy('category')]);
    }

    public function create()
    {
        return view('admin.skills.form', ['skill' => new Skill(['level' => 80, 'is_visible' => true]), 'categories' => $this->categories()]);
    }

    public function store(Request $request)
    {
        Skill::create($this->validated($request));

        return redirect()->route('admin.skills.index')->with('success', 'Skill added.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.form', ['skill' => $skill, 'categories' => $this->categories()]);
    }

    public function update(Request $request, Skill $skill)
    {
        $skill->update($this->validated($request));

        return redirect()->route('admin.skills.index')->with('success', 'Skill updated.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return back()->with('success', 'Skill deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'category' => ['required', 'string', 'max:60'],
            'level' => ['required', 'integer', 'between:0,100'],
            'icon' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $data + ['is_visible' => $request->boolean('is_visible'), 'sort_order' => $data['sort_order'] ?? 0];
    }

    private function categories()
    {
        return Skill::distinct()->orderBy('category')->pluck('category');
    }
}
