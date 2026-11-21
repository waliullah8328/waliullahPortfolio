<x-layouts.admin title="Messages">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div class="flex rounded-xl bg-slate-100 p-1 text-sm font-medium dark:bg-white/5">
            <a href="{{ route('admin.messages.index', ['q' => request('q')]) }}" class="rounded-lg px-4 py-1.5 {{ request('filter') !== 'unread' ? 'bg-white shadow dark:bg-white/10' : 'text-slate-500' }}">All</a>
            <a href="{{ route('admin.messages.index', ['filter' => 'unread', 'q' => request('q')]) }}" class="rounded-lg px-4 py-1.5 {{ request('filter') === 'unread' ? 'bg-white shadow dark:bg-white/10' : 'text-slate-500' }}">Unread</a>
        </div>
        <form method="GET" class="relative w-full max-w-xs">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search..." class="input pl-10">
        </form>
    </div>

    @if ($messages->isEmpty())
        <x-admin.empty icon="inbox" title="Inbox is empty">Messages sent through your website's contact form will show up here.</x-admin.empty>
    @else
        <div class="card divide-y divide-slate-100 overflow-hidden dark:divide-white/5">
            @foreach ($messages as $m)
                <a href="{{ route('admin.messages.show', $m) }}" class="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50 dark:hover:bg-white/[0.03] {{ $m->is_read ? '' : 'bg-accent/[0.04]' }}">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-accent/10 font-semibold text-accent">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-sm {{ $m->is_read ? 'font-medium text-slate-700 dark:text-slate-300' : 'font-bold text-slate-900 dark:text-white' }}">{{ $m->name }}</p>
                            <span class="hidden truncate text-xs text-slate-400 sm:inline">&lt;{{ $m->email }}&gt;</span>
                            @unless ($m->is_read)<span class="size-2 shrink-0 rounded-full bg-accent"></span>@endunless
                        </div>
                        <p class="truncate text-sm {{ $m->is_read ? 'text-slate-500' : 'font-medium text-slate-800 dark:text-slate-200' }}">{{ $m->subject ?: 'No subject' }}</p>
                        <p class="truncate text-sm text-slate-400">{{ Str::limit($m->message, 120) }}</p>
                    </div>
                    <span class="shrink-0 text-xs text-slate-400">{{ $m->created_at->diffForHumans() }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
</x-layouts.admin>
