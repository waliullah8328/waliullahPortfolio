<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Support\Media;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        return view('admin.certificates.index', ['certificates' => Certificate::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.certificates.form', ['certificate' => new Certificate(['is_visible' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = Media::replace($request->file('image'), null, 'certificates', $request->input('image_link'));

        Certificate::create($data);

        return redirect()->route('admin.certificates.index')->with('success', 'Certificate added.');
    }

    public function edit(Certificate $certificate)
    {
        return view('admin.certificates.form', compact('certificate'));
    }

    public function update(Request $request, Certificate $certificate)
    {
        $data = $this->validated($request);

        if ($request->boolean('remove_image')) {
            Media::delete($certificate->image);
            $certificate->image = null;
        }
        $data['image'] = Media::replace($request->file('image'), $certificate->image, 'certificates', $request->input('image_link'));

        $certificate->update($data);

        return redirect()->route('admin.certificates.index')->with('success', 'Certificate updated.');
    }

    public function destroy(Certificate $certificate)
    {
        Media::delete($certificate->image);
        $certificate->delete();

        return back()->with('success', 'Certificate deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'issuer' => ['required', 'string', 'max:150'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'credential_id' => ['nullable', 'string', 'max:150'],
            'credential_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_link' => ['nullable', 'url', 'max:2048'],
        ]);
        unset($data['image'], $data['image_link']);

        return array_merge($data, [
            'is_visible' => $request->boolean('is_visible'),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }
}
