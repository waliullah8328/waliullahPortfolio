<x-layouts.admin title="Project categories">
    <x-slot:actions>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost btn-sm"><x-icon name="folder" class="size-4" /> <span class="hidden sm:inline">Projects</span></a>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-[360px_1fr]">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="card h-fit space-y-4 p-6">
            @csrf
            <div>
                <p class="font-semibold text-slate-900 dark:text-white">Add a category</p>
                <p class="help">Then pick it on a project. Categories appear as filters on the Projects page.</p>
            </div>
            <div>
                <label class="label" for="name">Name *</label>
                <input id="name" name="name" value="{{ old('name') }}" class="input" required maxlength="60" placeholder="E-commerce, Education, Fintech...">
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="sort_order">Sort order</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input">
                <p class="help">Lower numbers appear first. Equal numbers: A to Z.</p>
            </div>
            <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4" /> Add category</button>
        </form>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                <h3 class="flex items-center gap-2 font-semibold"><x-icon name="tag" class="size-5 text-accent" /> All categories</h3>
                <span class="text-xs text-slate-500">{{ $categories->count() }} total</span>
            </div>

            @forelse ($categories as $cat)
                @php $used = $counts[$cat->name] ?? 0; @endphp
                <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 py-3 last:border-0 dark:border-white/5">
                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $cat->name }}" class="input min-w-40 flex-1 py-2!" required maxlength="60" aria-label="Category name">
                        <input name="sort_order" type="number" value="{{ $cat->sort_order }}" class="input w-20! py-2!" aria-label="Sort order" title="Sort order">
                        <span class="chip shrink-0" title="Projects in this category">{{ $used }} {{ str('project')->plural($used) }}</span>
                        <button class="btn btn-ghost btn-sm shrink-0"><x-icon name="check" class="size-4" /> Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                          data-confirm="Delete “{{ $cat->name }}”?{{ $used ? ' Its '.$used.' '.str('project')->plural($used).' will become uncategorized.' : '' }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm text-rose-500!" title="Delete"><x-icon name="trash" class="size-4" /></button>
                    </form>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-sm text-slate-500">No categories yet. Add your first one on the left.</div>
            @endforelse

            @if ($categories->isNotEmpty())
                <p class="border-t border-slate-100 bg-slate-50/60 px-5 py-3 text-xs text-slate-500 dark:border-white/5 dark:bg-white/[0.02]">
                    Renaming a category renames it on all its projects. Rename one to the name of another (e.g. “Educational” → “Education”) to merge them.
                </p>
            @endif
        </div>
    </div>
</x-layouts.admin>
