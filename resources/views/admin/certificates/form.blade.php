@php $editing = $certificate->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit certificate' : 'Add certificate'">
    <form method="POST" action="{{ $editing ? route('admin.certificates.update', $certificate) : route('admin.certificates.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[1fr_340px]">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card space-y-5 p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="label" for="title">Certificate name *</label>
                    <input id="title" name="title" value="{{ old('title', $certificate->title) }}" class="input" required placeholder="Flutter & Dart - The Complete Guide">
                </div>
                <div class="sm:col-span-2">
                    <label class="label" for="issuer">Issued by *</label>
                    <input id="issuer" name="issuer" value="{{ old('issuer', $certificate->issuer) }}" class="input" required placeholder="Udemy, Google, Coursera, Meta...">
                </div>
                <div>
                    <label class="label" for="issued_at">Issue date</label>
                    <input id="issued_at" type="date" name="issued_at" value="{{ old('issued_at', $certificate->issued_at?->format('Y-m-d')) }}" class="input">
                </div>
                <div>
                    <label class="label" for="expires_at">Expiry date</label>
                    <input id="expires_at" type="date" name="expires_at" value="{{ old('expires_at', $certificate->expires_at?->format('Y-m-d')) }}" class="input">
                    <p class="help">Leave empty if it never expires.</p>
                </div>
                <div>
                    <label class="label" for="credential_id">Credential ID</label>
                    <input id="credential_id" name="credential_id" value="{{ old('credential_id', $certificate->credential_id) }}" class="input" placeholder="UC-1a2b3c4d">
                </div>
                <div>
                    <label class="label" for="credential_url">Verification link</label>
                    <input id="credential_url" type="url" name="credential_url" value="{{ old('credential_url', $certificate->credential_url) }}" class="input" placeholder="https://">
                    <p class="help">Shows a “Verify” button on the site.</p>
                </div>
            </div>
            <div>
                <label class="label" for="skills">Skills covered</label>
                <input id="skills" name="skills" value="{{ old('skills', $certificate->skills) }}" class="input" placeholder="Flutter, Dart, Firebase">
                <p class="help">Comma separated.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card space-y-4 p-6">
                <x-admin.image-input name="image" label="Certificate image" :current="$certificate->image_url" :link="$certificate->image"
                    help="A photo or screenshot of the certificate. Landscape works best." />
            </div>
            <div class="card space-y-3 p-6">
                <x-admin.toggle name="is_visible" label="Visible" help="Show on the website" :checked="old('is_visible', $certificate->is_visible)" />
                <div>
                    <label class="label" for="sort_order">Sort order</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $certificate->sort_order ?? 0) }}" class="input">
                    <p class="help">Lower numbers appear first. Equal numbers: newest first.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-primary flex-1"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Add certificate' }}</button>
                <a href="{{ route('admin.certificates.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
