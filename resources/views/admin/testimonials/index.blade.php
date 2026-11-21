<x-layouts.admin title="Recommendations">
    <x-slot:actions>
        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Add</span></a>
    </x-slot:actions>

    <p class="mb-6 text-sm text-slate-500">Visitors can submit recommendations from your website. New ones stay <strong>pending</strong> until you approve them.</p>

    @if ($testimonials->isEmpty())
        <x-admin.empty icon="award" title="No recommendations yet" :action="route('admin.testimonials.create')" action-label="Add recommendation">Add quotes from clients, managers and colleagues.</x-admin.empty>
    @else
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($testimonials as $t)
                <div class="card flex flex-col p-5 {{ $t->is_visible ? '' : 'ring-2 ring-amber-400/50' }}">
                    <div class="flex items-center gap-3">
                        @if ($t->avatar_url)
                            <img src="{{ $t->avatar_url }}" alt="" class="size-11 rounded-full object-cover">
                        @else
                            <span class="grid size-11 place-items-center rounded-full bg-accent/15 text-sm font-bold text-accent">{{ $t->initials }}</span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $t->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ collect([$t->position, $t->company])->filter()->join(' · ') }}</p>
                        </div>
                        @unless ($t->is_visible)<span class="rounded-full bg-amber-400/15 px-2 py-0.5 text-[11px] font-bold text-amber-600">Pending</span>@endunless
                    </div>
                    <div class="mt-3 flex text-amber-400">
                        @for ($i = 1; $i <= 5; $i++)<x-icon name="star" class="size-4 {{ $i <= $t->rating ? 'fill-current' : 'text-slate-300 dark:text-white/15' }}" />@endfor
                    </div>
                    <p class="mt-3 line-clamp-4 flex-1 text-sm text-slate-600 dark:text-slate-400">“{{ $t->content }}”</p>
                    <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-4 dark:border-white/5">
                        <form method="POST" action="{{ route('admin.testimonials.toggle', $t) }}" class="flex-1">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm w-full {{ $t->is_visible ? 'btn-ghost' : 'btn-primary' }}">
                                <x-icon :name="$t->is_visible ? 'eye-off' : 'check'" class="size-4" /> {{ $t->is_visible ? 'Hide' : 'Approve' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn btn-ghost btn-sm" title="Edit"><x-icon name="edit" class="size-4" /></a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" data-confirm="Delete this recommendation?">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-rose-500!" title="Delete"><x-icon name="trash" class="size-4" /></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
