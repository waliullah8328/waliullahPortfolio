<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index()
    {
        return view('admin.experiences.index', ['experiences' => Experience::ordered()->get()->groupBy('type')]);
    }

    public function create(Request $request)
    {
        return view('admin.experiences.form', ['experience' => new Experience(['type' => $request->get('type', 'work')])]);
    }

    public function store(Request $request)
    {
        Experience::create($this->validated($request));

        return redirect()->route('admin.experiences.index')->with('success', 'Entry added.');
    }

    public function edit(Experience $experience)
    {
        return view('admin.experiences.form', compact('experience'));
    }

    public function update(Request $request, Experience $experience)
    {
        $experience->update($this->validated($request));

        return redirect()->route('admin.experiences.index')->with('success', 'Entry updated.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return back()->with('success', 'Entry deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:work,education'],
            'title' => ['required', 'string', 'max:150'],
            'organization' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $current = $request->boolean('is_current');

        return array_merge($data, [
            'is_current' => $current,
            'end_date' => $current ? null : ($data['end_date'] ?? null),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }
}
