@props(['title' => 'Dashboard', 'actions' => null])
@php
    $unread = \App\Models\Message::where('is_read', false)->count();
    $pending = \App\Models\Testimonial::where('is_visible', false)->count();
    $groups = [
        'Overview' => [
            ['Dashboard', 'admin.dashboard', 'grid', null, 'admin.dashboard'],
        ],
        'Content' => [
            ['Projects', 'admin.projects.index', 'folder', null, 'admin.projects.*'],
            ['Services', 'admin.services.index', 'sparkles', null, 'admin.services.*'],
            ['Skills', 'admin.skills.index', 'layers', null, 'admin.skills.*'],
            ['Experience', 'admin.experiences.index', 'briefcase', null, 'admin.experiences.*'],
            ['Blog', 'admin.posts.index', 'file-text', null, 'admin.posts.*'],
        ],
        'Inbox' => [
            ['Recommendations', 'admin.testimonials.index', 'award', $pending, 'admin.testimonials.*'],
            ['Messages', 'admin.messages.index', 'inbox', $unread, 'admin.messages.*'],
        ],
        'System' => [
            ['Settings', 'admin.settings', 'settings', null, 'admin.settings'],
            ['Account', 'admin.account', 'user', null, 'admin.account'],
        ],
    ];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en" class="dark" style="--accent: {{ setting('accent_color', '#7c3aed') }}; --accent-2: {{ setting('accent_color_2', '#06b6d4') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>{{ $title }} · Admin</title>
    <script>
        (function () { var t = null; try { t = localStorage.getItem('theme'); } catch (e) {} document.documentElement.classList.toggle('dark', t ? t === 'dark' : true); })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell min-h-screen font-sans">
    {{-- Ambient background --}}
    <div aria-hidden="true" class="admin-ambient">
        <span class="admin-glow admin-glow-1"></span>
        <span class="admin-glow admin-glow-2"></span>
        <span class="admin-grain"></span>
    </div>

    <div class="relative flex min-h-screen">
        {{-- Sidebar --}}
        <aside id="admin-sidebar" class="admin-sidebar hidden lg:flex [&:not(.hidden)]:flex">
            <div class="flex items-center gap-3 px-3 pt-2 pb-5">
                <span class="admin-logo"><x-icon name="sparkles" class="size-5" /></span>
                <div class="min-w-0 leading-tight">
                    <p class="font-display font-bold tracking-tight text-slate-900 dark:text-white">Portfolio CMS</p>
                    <p class="truncate text-xs text-slate-500">{{ setting('name', 'Admin') }}</p>
                </div>
                <button type="button" data-toggle="admin-sidebar" class="ml-auto grid size-8 place-items-center rounded-lg hover:bg-slate-900/5 lg:hidden dark:hover:bg-white/10" aria-label="Close menu"><x-icon name="x" class="size-4" /></button>
            </div>

            <nav class="-mx-1 flex-1 space-y-5 overflow-y-auto px-1">
                @foreach ($groups as $group => $items)
                    <div>
                        <p class="admin-nav-label">{{ $group }}</p>
                        <div class="space-y-0.5">
                            @foreach ($items as [$label, $route, $icon, $badge, $pattern])
                                <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                                    <x-icon :name="$icon" class="size-[18px]" />
                                    <span class="flex-1">{{ $label }}</span>
                                    @if ($badge)
                                        <span class="admin-badge">{{ $badge }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="mt-4 space-y-1 border-t border-slate-900/[0.06] pt-4 dark:border-white/[0.06]">
                <a href="{{ route('home') }}" target="_blank" class="side-link"><x-icon name="external" class="size-[18px]" /> View website</a>
                <div class="flex items-center gap-3 rounded-2xl bg-slate-900/[0.03] p-2.5 dark:bg-white/[0.04]">
                    <span class="admin-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-1 leading-tight">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="grid size-8 cursor-pointer place-items-center rounded-lg text-slate-500 transition hover:bg-rose-500/10 hover:text-rose-500" title="Log out" aria-label="Log out"><x-icon name="logout" class="size-4" /></button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-[18.5rem]">
            <header class="admin-header">
                <button type="button" data-toggle="admin-sidebar" class="admin-icon-btn lg:hidden" aria-label="Open menu"><x-icon name="menu" class="size-5" /></button>
                <div class="min-w-0">
                    <p class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">Admin</a>
                        <x-icon name="chevron-right" class="size-3" />
                        <span class="truncate">{{ $title }}</span>
                    </p>
                    <h1 class="truncate text-xl font-bold tracking-tight">{{ $title }}</h1>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    {{ $actions }}
                    <button type="button" data-theme-toggle class="admin-icon-btn" aria-label="Toggle theme">
                        <x-icon name="sun" class="hidden size-5 dark:block" /><x-icon name="moon" class="size-5 dark:hidden" />
                    </button>
                </div>
            </header>

            <main class="admin-main flex-1 px-4 pb-10 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div data-flash class="admin-toast admin-toast-success mb-6">
                        <span class="grid size-7 place-items-center rounded-full bg-emerald-500 text-white"><x-icon name="check" class="size-4" /></span>
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="admin-toast admin-toast-error mb-6 items-start">
                        <span class="grid size-7 shrink-0 place-items-center rounded-full bg-rose-500 text-white"><x-icon name="x" class="size-4" /></span>
                        <div>
                            <p class="font-semibold">Please fix the following:</p>
                            <ul class="mt-1 list-disc pl-5 font-normal">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
