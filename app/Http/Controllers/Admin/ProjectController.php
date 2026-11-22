<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\Media;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.form', ['project' => new Project(['is_published' => true, 'type' => 'website'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Project::class, $request->slug, $data['title']);
        $data['image'] = Media::replace($request->file('image'), null, 'projects', $request->input('image_link'));
        $data['screen_image'] = Media::replace($request->file('screen_image'), null, 'projects', $request->input('screen_image_link'));

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Project::class, $request->slug, $data['title'], $project);

        if ($request->boolean('remove_image')) {
            Media::delete($project->image);
            $project->image = null;
        }
        $data['image'] = Media::replace($request->file('image'), $project->image, 'projects', $request->input('image_link'));

        if ($request->boolean('remove_screen_image')) {
            Media::delete($project->screen_image);
            $project->screen_image = null;
        }
        $data['screen_image'] = Media::replace($request->file('screen_image'), $project->screen_image, 'projects', $request->input('screen_image_link'));

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        Media::delete($project->image);
        Media::delete($project->screen_image);
        $project->delete();

        return back()->with('success', 'Project deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:60'],
            'type' => ['required', 'in:'.implode(',', array_keys(Project::TYPES))],
            'app_store_url' => ['nullable', 'url', 'max:255'],
            'play_store_url' => ['nullable', 'url', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'tech_stack' => ['nullable', 'string', 'max:255'],
            'live_url' => ['nullable', 'url', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_link' => ['nullable', 'url', 'max:2048'],
            'screen_image' => ['nullable', 'image', 'max:5120'],
            'screen_image_link' => ['nullable', 'url', 'max:2048'],
        ]);
        unset($data['image'], $data['image_link'], $data['screen_image'], $data['screen_image_link'], $data['slug']);

        if ($data['type'] === 'website') {
            $data['app_store_url'] = $data['play_store_url'] = null;
        }

        return $data + [
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $data['sort_order'] ?? 0,
        ];
    }
}
