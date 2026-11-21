@php $editing = $skill->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit skill' : 'Add skill'">
    <form method="POST" action="{{ $editing ? route('admin.skills.update', $skill) : route('admin.skills.store') }}" class="card max-w-2xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="name">Skill name *</label>
                <input id="name" name="name" value="{{ old('name', $skill->name) }}" class="input" required placeholder="Laravel">
            </div>
            <div>
                <label class="label" for="category">Category *</label>
                <input id="category" name="category" value="{{ old('category', $skill->category) }}" class="input" required list="skill-cats" placeholder="Backend">
                <datalist id="skill-cats">@foreach ($categories as $c)<option value="{{ $c }}">@endforeach</datalist>
            </div>
        </div>
        <div>
            <label class="label" for="level">Proficiency: <output id="level-out" class="font-bold text-accent">{{ old('level', $skill->level) }}</output>%</label>
            <input id="level" type="range" name="level" min="0" max="100" step="5" value="{{ old('level', $skill->level) }}" class="w-full accent-[var(--accent)]" oninput="document.getElementById('level-out').textContent = this.value">
        </div>
        <div>
            <label class="label" for="icon">Icon URL (optional)</label>
            <input id="icon" name="icon" value="{{ old('icon', $skill->icon) }}" class="input" placeholder="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg">
            <p class="help">Any SVG/PNG link. Tip: <a href="https://devicon.dev" target="_blank" class="text-accent underline">devicon.dev</a> has icons for most technologies.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="label" for="sort_order">Sort order</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $skill->sort_order ?? 0) }}" class="input">
            </div>
            <div class="sm:pt-6"><x-admin.toggle name="is_visible" label="Visible on site" :checked="old('is_visible', $skill->is_visible)" /></div>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Add skill' }}</button>
            <a href="{{ route('admin.skills.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
