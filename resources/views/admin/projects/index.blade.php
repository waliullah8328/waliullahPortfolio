<x-layouts.admin title="Projects">
    <x-slot:actions>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">New project</span></a>
    </x-slot:actions>

    <form method="GET" class="relative mb-6 max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search projects..." class="input pl-10">
    </form>

    @if ($projects->isEmpty())
        <x-admin.empty icon="folder" title="No projects yet" :action="route('admin.projects.create')" action-label="Add your first project">Showcase your best work with images, tech stack and links.</x-admin.empty>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <div class="card flex flex-col overflow-hidden">
                    <div class="relative aspect-[16/9] bg-slate-100 dark:bg-white/5">
                        @if ($project->image_url)
                            <img src="{{ $project->image_url }}" alt="" class="size-full object-cover">
                        @else
                            <div class="grid size-full place-items-center text-slate-400"><x-icon name="image" class="size-10" /></div>
                        @endif
                        <div class="absolute left-3 top-3 flex gap-1.5">
                            @if ($project->is_featured)<span class="rounded-full bg-amber-400 px-2 py-0.5 text-[11px] font-bold text-amber-950">Featured</span>@endif
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $project->is_published ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white' }}">{{ $project->is_published ? 'Published' : 'Hidden' }}</span>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-accent">
                            <x-icon :name="$project->is_app ? 'smartphone' : 'globe'" class="size-3.5" /> {{ $project->type_label }}
                            <span class="text-slate-400">· {{ $project->category ?: 'Uncategorized' }}</span>
                        </p>
                        <h3 class="mt-1 font-semibold">{{ $project->title }}</h3>
                        <p class="mt-1 line-clamp-2 flex-1 text-sm text-slate-500">{{ $project->summary }}</p>
                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-ghost btn-sm flex-1"><x-icon name="edit" class="size-4" /> Edit</a>
                            <a href="{{ route('projects.show', $project) }}" target="_blank" class="btn btn-ghost btn-sm" title="View"><x-icon name="eye" class="size-4" /></a>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" data-confirm="Delete “{{ $project->title }}”?">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-500! hover:border-rose-500!" title="Delete"><x-icon name="trash" class="size-4" /></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $projects->links() }}</div>
    @endif
</x-layouts.admin>
