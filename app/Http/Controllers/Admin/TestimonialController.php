<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\Media;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::orderBy('is_visible')->ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial(['rating' => 5, 'is_visible' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['avatar'] = Media::replace($request->file('avatar'), null, 'testimonials', $request->input('avatar_link'));
        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Recommendation added.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);
        if ($request->boolean('remove_image')) {
            Media::delete($testimonial->avatar);
            $testimonial->avatar = null;
        }
        $data['avatar'] = Media::replace($request->file('avatar'), $testimonial->avatar, 'testimonials', $request->input('avatar_link'));
        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Recommendation updated.');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_visible' => ! $testimonial->is_visible]);

        return back()->with('success', $testimonial->is_visible ? 'Approved and now visible.' : 'Hidden from the site.');
    }

    public function destroy(Testimonial $testimonial)
    {
        Media::delete($testimonial->avatar);
        $testimonial->delete();

        return back()->with('success', 'Recommendation deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'sort_order' => ['nullable', 'integer'],
            'avatar' => ['nullable', 'image', 'max:3072'],
            'avatar_link' => ['nullable', 'url', 'max:2048'],
        ]);
        unset($data['avatar'], $data['avatar_link']);

        return $data + ['is_visible' => $request->boolean('is_visible'), 'sort_order' => $data['sort_order'] ?? 0];
    }
}
