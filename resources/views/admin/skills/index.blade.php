<x-layouts.admin title="Skills">
    <x-slot:actions>
        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Add skill</span></a>
    </x-slot:actions>

    @if ($skills->isEmpty())
        <x-admin.empty icon="layers" title="No skills yet" :action="route('admin.skills.create')" action-label="Add a skill">Group skills by category and set a proficiency level.</x-admin.empty>
    @else
        <div class="grid gap-6 lg:grid-cols-2">
            @foreach ($skills as $category => $items)
                <div class="card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-white/10">
                        <h3 class="font-semibold">{{ $category }}</h3>
                        <span class="chip">{{ $items->count() }}</span>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($items as $skill)
                            <li class="flex items-center gap-4 px-5 py-3">
                                @if ($skill->icon)
                                    <img src="{{ $skill->icon }}" alt="" class="size-6">
                                @else
                                    <span class="grid size-6 place-items-center rounded-md bg-accent/10 text-[10px] font-bold text-accent">{{ mb_substr($skill->name, 0, 2) }}</span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium {{ $skill->is_visible ? 'text-slate-900 dark:text-white' : 'text-slate-400 line-through' }}">{{ $skill->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $skill->level }}%</span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-white/10">
                                        <div class="h-full rounded-full" style="width: {{ $skill->level }}%; background-image: linear-gradient(90deg, var(--accent), var(--accent-2))"></div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.skills.edit', $skill) }}" class="text-slate-400 hover:text-accent" title="Edit"><x-icon name="edit" class="size-4" /></a>
                                <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" data-confirm="Delete {{ $skill->name }}?">
                                    @csrf @method('DELETE')
                                    <button class="cursor-pointer text-slate-400 hover:text-rose-500" title="Delete"><x-icon name="trash" class="size-4" /></button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
