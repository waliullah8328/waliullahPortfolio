<x-layouts.admin title="Dashboard">
    @php
        $max = max(1, $messagesChart->max('count'));
        $weekTotal = $messagesChart->sum('count');
        $hour = (int) now()->format('G');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp

    <div class="bento">
        {{-- Hero --}}
        <section class="relative isolate overflow-hidden rounded-3xl p-6 text-white sm:p-8 lg:col-span-8!" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">
            <div aria-hidden="true" class="absolute inset-0 -z-10 opacity-30" style="background-image: radial-gradient(circle at 85% 15%, white 0, transparent 35%), radial-gradient(circle at 10% 110%, white 0, transparent 40%)"></div>
            <div aria-hidden="true" class="bg-grid absolute inset-0 -z-10 opacity-40"></div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                <x-icon name="calendar" class="size-3.5" /> {{ now()->format('l, F j') }}
            </span>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $greeting }}, {{ Str::before(auth()->user()->name, ' ') }}.</h2>
            <p class="mt-2 max-w-lg text-white/85">Here's what's happening with your portfolio. Manage projects, skills, CV, experience, blog and messages from one place.</p>
            <div class="mt-7 flex flex-wrap gap-2">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-sm bg-white text-slate-900 shadow-lg shadow-black/10 hover:-translate-y-0.5 hover:bg-white"><x-icon name="plus" class="size-4" /> New project</a>
                <a href="{{ route('admin.posts.create') }}" class="btn btn-sm bg-white/15 text-white backdrop-blur hover:bg-white/25"><x-icon name="edit" class="size-4" /> Write post</a>
                <a href="{{ route('admin.settings', ['tab' => 'profile']) }}" class="btn btn-sm bg-white/15 text-white backdrop-blur hover:bg-white/25"><x-icon name="upload" class="size-4" /> Update CV</a>
            </div>
        </section>

        {{-- Weekly messages chart --}}
        <section class="card flex flex-col p-6 lg:col-span-4!">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Messages · last 7 days</p>
                    <p class="mt-1 font-display text-4xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $weekTotal }}</p>
                </div>
                <span class="stat-icon grid size-11 place-items-center rounded-2xl bg-accent/10 text-accent"><x-icon name="inbox" class="size-5" /></span>
            </div>
            <div class="mt-auto flex h-32 items-end gap-2 pt-6">
                @foreach ($messagesChart as $d)
                    <div class="group flex flex-1 flex-col items-center gap-2" title="{{ $d['count'] }} on {{ $d['day'] }}">
                        <span class="text-[11px] font-semibold text-slate-500 opacity-0 transition group-hover:opacity-100">{{ $d['count'] }}</span>
                        <div class="w-full rounded-xl transition-all duration-300 group-hover:opacity-100 {{ $d['count'] ? 'opacity-85' : 'opacity-25' }}" style="height: {{ max(6, $d['count'] / $max * 88) }}px; background-image: linear-gradient(to top, var(--accent), var(--accent-2))"></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ $d['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Stat tiles --}}
        @foreach ($stats as $s)
            <a href="{{ route($s['route']) }}" class="card card-hover stat-tile col-span-6! md:col-span-4! xl:col-span-2!">
                <div class="flex items-center justify-between">
                    <span class="stat-icon"><x-icon :name="$s['icon']" class="size-5" /></span>
                    <x-icon name="arrow-up-right" class="size-4 text-slate-400" />
                </div>
                <p class="mt-5 font-display text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $s['value'] }}</p>
                <p class="text-sm text-slate-500">{{ $s['label'] }}</p>
            </a>
        @endforeach

        {{-- Recent messages --}}
        <section class="card p-6 lg:col-span-7!">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Recent messages</h3>
                <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-accent">View all <x-icon name="arrow-right" class="size-3.5" /></a>
            </div>
            <ul class="mt-4 space-y-1">
                @forelse ($recentMessages as $m)
                    <li>
                        <a href="{{ route('admin.messages.show', $m) }}" class="-mx-3 flex items-center gap-4 rounded-2xl px-3 py-3 transition hover:bg-slate-900/[0.03] dark:hover:bg-white/[0.04]">
                            <span class="admin-avatar size-10! rounded-full!">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $m->name }} @unless($m->is_read)<span class="rounded-full bg-accent/15 px-1.5 py-px text-[10px] font-bold text-accent">NEW</span>@endunless
                                </p>
                                <p class="truncate text-sm text-slate-500">{{ $m->subject ?: Str::limit($m->message, 80) }}</p>
                            </div>
                            <span class="shrink-0 text-xs text-slate-400">{{ $m->created_at->diffForHumans(short: true) }}</span>
                        </a>
                    </li>
                @empty
                    <li class="grid place-items-center gap-2 py-10 text-center text-sm text-slate-500">
                        <span class="stat-icon grid size-12 place-items-center rounded-2xl bg-accent/10 text-accent"><x-icon name="inbox" class="size-5" /></span>
                        No messages yet. Queries from your contact form will appear here.
                    </li>
                @endforelse
            </ul>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:col-span-5! lg:grid-cols-2 lg:content-start">
            <div class="card stat-tile">
                <span class="stat-icon"><x-icon name="eye" class="size-5" /></span>
                <p class="mt-5 font-display text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($blogViews) }}</p>
                <p class="text-sm text-slate-500">Blog views</p>
            </div>
            <a href="{{ route('admin.testimonials.index') }}" class="card card-hover stat-tile">
                <span class="stat-icon"><x-icon name="award" class="size-5" /></span>
                <p class="mt-5 font-display text-3xl font-bold tracking-tight {{ $pendingTestimonials ? 'text-amber-500' : 'text-slate-900 dark:text-white' }}">{{ $pendingTestimonials }}</p>
                <p class="text-sm text-slate-500">Pending reviews</p>
            </a>

            <section class="card p-6 sm:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">Recent posts</h3>
                    <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-accent">All <x-icon name="arrow-right" class="size-3.5" /></a>
                </div>
                <ul class="mt-3 space-y-1">
                    @forelse ($recentPosts as $p)
                        <li>
                            <a href="{{ route('admin.posts.edit', $p) }}" class="-mx-2 flex items-center justify-between gap-3 rounded-xl px-2 py-2 text-sm transition hover:bg-slate-900/[0.03] dark:hover:bg-white/[0.04]">
                                <span class="truncate font-medium text-slate-800 dark:text-slate-200">{{ $p->title }}</span>
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $p->is_published ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-500/10 text-slate-500' }}">
                                    <span class="size-1.5 rounded-full {{ $p->is_published ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>{{ $p->is_published ? 'Live' : 'Draft' }}
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-slate-500">No posts yet.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-layouts.admin>
