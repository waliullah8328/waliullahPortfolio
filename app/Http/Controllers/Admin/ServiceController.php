<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', ['services' => Service::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(['icon' => 'smartphone', 'is_visible' => true])]);
    }

    public function store(Request $request)
    {
        Service::create($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Service added.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $service->update($this->validated($request));

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Service deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:600'],
            'icon' => ['required', 'in:'.implode(',', Service::ICONS)],
            'tags' => ['nullable', 'string', 'max:200'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        return $data + ['is_visible' => $request->boolean('is_visible'), 'sort_order' => $data['sort_order'] ?? 0];
    }
}
