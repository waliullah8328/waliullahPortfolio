<x-layouts.admin title="Experience & Education">
    <x-slot:actions>
        <a href="{{ route('admin.experiences.create', ['type' => 'education']) }}" class="btn btn-ghost btn-sm"><x-icon name="graduation" class="size-4" /> <span class="hidden sm:inline">Education</span></a>
        <a href="{{ route('admin.experiences.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Work</span></a>
    </x-slot:actions>

    @if ($experiences->isEmpty())
        <x-admin.empty icon="briefcase" title="No entries yet" :action="route('admin.experiences.create')" action-label="Add experience">Add jobs, freelance work, internships and education.</x-admin.empty>
    @else
        <div class="grid gap-6 xl:grid-cols-2">
            @foreach (['work' => ['Work experience', 'briefcase'], 'education' => ['Education', 'graduation']] as $type => [$label, $icon])
                <div class="card overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <x-icon :name="$icon" class="size-5 text-accent" /><h3 class="font-semibold">{{ $label }}</h3>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($experiences->get($type, collect()) as $exp)
                            <li class="flex items-start gap-4 px-5 py-4">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $exp->title }}</p>
                                    <p class="text-sm text-slate-500">{{ $exp->organization }}@if($exp->location) · {{ $exp->location }}@endif</p>
                                    @if ($exp->address)<p class="text-xs text-slate-400">{{ $exp->address }}</p>@endif
                                    <p class="mt-1 text-xs font-medium text-accent">{{ $exp->period }}</p>
                                </div>
                                <a href="{{ route('admin.experiences.edit', $exp) }}" class="text-slate-400 hover:text-accent" title="Edit"><x-icon name="edit" class="size-4" /></a>
                                <form method="POST" action="{{ route('admin.experiences.destroy', $exp) }}" data-confirm="Delete this entry?">
                                    @csrf @method('DELETE')
                                    <button class="cursor-pointer text-slate-400 hover:text-rose-500" title="Delete"><x-icon name="trash" class="size-4" /></button>
                                </form>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-slate-500">None yet.</li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
