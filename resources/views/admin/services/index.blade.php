<x-layouts.admin title="Services">
    <x-slot:actions>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Add service</span></a>
    </x-slot:actions>

    <p class="mb-6 text-sm text-slate-500">These cards appear in the <strong>What I do</strong> section of your home page.</p>

    @if ($services->isEmpty())
        <x-admin.empty icon="sparkles" title="No services yet" :action="route('admin.services.create')" action-label="Add a service">Describe what you offer, e.g. cross-platform apps, UI/UX, store release.</x-admin.empty>
    @else
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($services as $service)
                <div class="card flex flex-col p-5 {{ $service->is_visible ? '' : 'opacity-60' }}">
                    <div class="flex items-start gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"><x-icon :name="$service->icon" class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $service->title }}</p>
                            @unless ($service->is_visible)<p class="text-xs font-semibold text-slate-500">Hidden</p>@endunless
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-3 flex-1 text-sm text-slate-600 dark:text-slate-400">{{ $service->description }}</p>
                    <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-4 dark:border-white/5">
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-ghost btn-sm flex-1"><x-icon name="edit" class="size-4" /> Edit</a>
                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-confirm="Delete {{ $service->title }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-500!" title="Delete"><x-icon name="trash" class="size-4" /></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
