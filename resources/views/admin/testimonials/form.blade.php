@php $editing = $testimonial->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit recommendation' : 'Add recommendation'">
    <form method="POST" action="{{ $editing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-3">
            <div><label class="label" for="name">Name *</label><input id="name" name="name" value="{{ old('name', $testimonial->name) }}" class="input" required></div>
            <div><label class="label" for="position">Position</label><input id="position" name="position" value="{{ old('position', $testimonial->position) }}" class="input" placeholder="CTO"></div>
            <div><label class="label" for="company">Company</label><input id="company" name="company" value="{{ old('company', $testimonial->company) }}" class="input"></div>
        </div>
        <div>
            <label class="label" for="content">Recommendation *</label>
            <textarea id="content" name="content" rows="5" class="input" required>{{ old('content', $testimonial->content) }}</textarea>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <span class="label">Rating</span>
                <div class="flex gap-1 text-slate-300 dark:text-white/20" data-rating>
                    <input type="hidden" name="rating" value="{{ old('rating', $testimonial->rating) }}">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" class="cursor-pointer" aria-label="{{ $i }} stars"><x-icon name="star" class="size-7 fill-current" /></button>
                    @endfor
                </div>
            </div>
            <div><label class="label" for="sort_order">Sort order</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" class="input"></div>
        </div>
        <x-admin.image-input name="avatar" label="Photo" :current="$testimonial->avatar_url" :link="$testimonial->avatar" />
        <x-admin.toggle name="is_visible" label="Visible on website" :checked="old('is_visible', $testimonial->is_visible)" />
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Add recommendation' }}</button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
