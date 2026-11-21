@php $editing = $service->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit service' : 'Add service'">
    <form method="POST" action="{{ $editing ? route('admin.services.update', $service) : route('admin.services.store') }}" class="card max-w-3xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div>
            <label class="label" for="title">Title *</label>
            <input id="title" name="title" value="{{ old('title', $service->title) }}" class="input" required placeholder="Cross-platform App Development">
        </div>
        <div>
            <label class="label" for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="input" maxlength="600">{{ old('description', $service->description) }}</textarea>
        </div>
        <div>
            <span class="label">Icon</span>
            <div class="grid grid-cols-7 gap-2">
                @foreach (\App\Models\Service::ICONS as $icon)
                    <label class="grid aspect-square cursor-pointer place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:border-accent has-[:checked]:border-accent has-[:checked]:bg-accent/10 has-[:checked]:text-accent dark:border-white/10" title="{{ $icon }}">
                        <input type="radio" name="icon" value="{{ $icon }}" class="sr-only" @checked(old('icon', $service->icon) === $icon)>
                        <x-icon :name="$icon" class="size-5" />
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="label" for="tags">Tags</label>
            <input id="tags" name="tags" value="{{ old('tags', $service->tags) }}" class="input" placeholder="Flutter, iOS, Android">
            <p class="help">Comma separated. Shown as small chips on the card.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label" for="sort_order">Sort order</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}" class="input"></div>
            <div class="sm:pt-6"><x-admin.toggle name="is_visible" label="Visible on site" :checked="old('is_visible', $service->is_visible)" /></div>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Add service' }}</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
