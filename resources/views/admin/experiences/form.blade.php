@php $editing = $experience->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit entry' : 'Add experience / education'">
    <form method="POST" action="{{ $editing ? route('admin.experiences.update', $experience) : route('admin.experiences.store') }}" class="card max-w-3xl space-y-5 p-6 sm:p-8">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div>
            <span class="label">Type</span>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['work' => ['Work', 'briefcase'], 'education' => ['Education', 'graduation']] as $val => [$lbl, $icon])
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 has-[:checked]:border-accent has-[:checked]:bg-accent/5 dark:border-white/10">
                        <input type="radio" name="type" value="{{ $val }}" @checked(old('type', $experience->type) === $val) class="sr-only">
                        <x-icon :name="$icon" class="size-5 text-accent" /><span class="text-sm font-medium">{{ $lbl }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label" for="title">Job title / Degree *</label><input id="title" name="title" value="{{ old('title', $experience->title) }}" class="input" required></div>
            <div><label class="label" for="organization">Company / Institution *</label><input id="organization" name="organization" value="{{ old('organization', $experience->organization) }}" class="input" required></div>
            <div><label class="label" for="location">Location</label><input id="location" name="location" value="{{ old('location', $experience->location) }}" class="input" placeholder="Remote"></div>
            <div><label class="label" for="sort_order">Sort order</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $experience->sort_order ?? 0) }}" class="input"></div>
            <div><label class="label" for="start_date">Start date *</label><input id="start_date" type="date" name="start_date" value="{{ old('start_date', $experience->start_date?->format('Y-m-d')) }}" class="input" required></div>
            <div><label class="label" for="end_date">End date</label><input id="end_date" type="date" name="end_date" value="{{ old('end_date', $experience->end_date?->format('Y-m-d')) }}" class="input"></div>
        </div>
        <x-admin.toggle name="is_current" label="I currently work / study here" help="Shows “Present” instead of an end date" :checked="old('is_current', $experience->is_current)" />
        <div>
            <label class="label" for="description">Description / achievements</label>
            <textarea id="description" name="description" rows="6" class="input" placeholder="- Built a ...&#10;- Improved ... by 40%">{{ old('description', $experience->description) }}</textarea>
            <p class="help">One point per line. Each line becomes a bullet on the site.</p>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Add entry' }}</button>
            <a href="{{ route('admin.experiences.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
