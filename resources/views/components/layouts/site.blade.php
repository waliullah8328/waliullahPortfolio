@props(['title' => null, 'description' => null, 'image' => null])
@php
    $name = setting('name', 'Your Name');
    $theme = setting('default_theme', 'dark');
    $home = request()->routeIs('home') ? '' : route('home');
    $nav = collect([
        ['label' => 'About', 'href' => $home.'#about', 'icon' => 'user', 'section' => 'about', 'on' => setting_on('show_about')],
        ['label' => 'Services', 'href' => $home.'#services', 'icon' => 'sparkles', 'section' => 'services', 'on' => setting_on('show_services')],
        ['label' => 'Skills', 'href' => $home.'#skills', 'icon' => 'layers', 'section' => 'skills', 'on' => setting_on('show_skills')],
        ['label' => 'Experience', 'href' => $home.'#experience', 'icon' => 'briefcase', 'section' => 'experience', 'on' => setting_on('show_experience')],
        ['label' => 'Certificates', 'href' => $home.'#certificates', 'icon' => 'badge-check', 'section' => 'certificates', 'on' => setting_on('show_certificates') && \App\Models\Certificate::visible()->exists()],
        ['label' => 'Projects', 'href' => route('projects.index'), 'icon' => 'folder', 'route' => 'projects.*', 'on' => setting_on('show_projects')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'icon' => 'file-text', 'route' => 'blog.*', 'on' => setting_on('show_blog')],
        ['label' => 'Contact', 'href' => $home.'#contact', 'icon' => 'mail', 'section' => 'contact', 'on' => setting_on('show_contact')],
    ])->where('on', true);
    $initials = collect(explode(' ', $name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $theme === 'dark' ? 'dark' : '' }}"
      style="--accent: {{ setting('accent_color', '#7c3aed') }}; --accent-2: {{ setting('accent_color_2', '#06b6d4') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · '.$name : setting('site_title', $name.' · Portfolio') }}</title>
    <meta name="description" content="{{ $description ?? setting('meta_description', setting('hero_intro')) }}">
    <meta property="og:title" content="{{ $title ?? setting('site_title', $name) }}">
    <meta property="og:description" content="{{ $description ?? setting('meta_description') }}">
    @if ($ogImage = ($image ?? media(setting('avatar'))))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if (setting('favicon'))
        <link rel="icon" href="{{ media(setting('favicon')) }}">
    @endif
    <script>
        (function () {
            var t = null; try { t = localStorage.getItem('theme'); } catch (e) {}
            var def = @json($theme);
            var dark = t ? t === 'dark' : (def === 'system' ? matchMedia('(prefers-color-scheme: dark)').matches : def === 'dark');
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [data-header].scrolled { background: color-mix(in oklab, var(--color-white) 75%, transparent); box-shadow: 0 1px 0 rgb(0 0 0 / .06); }
        .dark [data-header].scrolled { background: rgb(7 7 13 / .7); box-shadow: 0 1px 0 rgb(255 255 255 / .06); }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden font-sans">
    <header data-header class="fixed inset-x-0 top-0 z-50 backdrop-blur-xl transition-all duration-300">
        <nav class="container-x flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-display text-lg font-bold text-slate-900 dark:text-white">
                <span class="grid size-9 place-items-center rounded-xl text-sm text-white shadow-lg shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">
                    {{ $initials }}
                </span>
                <span class="hidden sm:inline">{{ $name }}</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}" class="nav-link">{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                <button type="button" data-theme-toggle class="grid size-10 place-items-center rounded-xl text-slate-600 transition hover:bg-slate-200/60 dark:text-slate-300 dark:hover:bg-white/10" aria-label="Toggle dark mode">
                    <x-icon name="sun" class="hidden size-5 dark:block" />
                    <x-icon name="moon" class="size-5 dark:hidden" />
                </button>
                @if (setting('cv'))
                    <a href="{{ route('cv') }}" class="btn btn-primary btn-sm hidden sm:inline-flex"><x-icon name="download" class="size-4" /> Resume</a>
                @endif
                <button type="button" data-drawer-open aria-controls="mobile-drawer" aria-expanded="false" aria-label="Open menu" class="drawer-burger md:hidden">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>
    </header>

    {{-- Mobile drawer --}}
    <div id="mobile-drawer" class="drawer md:hidden" data-drawer aria-hidden="true">
        <div class="drawer-backdrop" data-drawer-close></div>
        <aside class="drawer-panel" role="dialog" aria-modal="true" aria-label="Menu">
            <div class="drawer-glow" aria-hidden="true"></div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                    <span class="grid size-11 shrink-0 place-items-center rounded-2xl font-display text-sm font-bold text-white shadow-lg shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">{{ $initials }}</span>
                    <span class="min-w-0">
                        <span class="block truncate font-display font-bold text-slate-900 dark:text-white">{{ $name }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ setting('role', 'Developer') }}</span>
                    </span>
                </a>
                <button type="button" class="drawer-close" data-drawer-close aria-label="Close menu"><x-icon name="x" class="size-5" /></button>
            </div>

            <p class="mt-7 mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Menu</p>
            <nav class="drawer-nav">
                @foreach ($nav as $item)
                    @php $active = isset($item['route']) && request()->routeIs($item['route']); @endphp
                    <a href="{{ $item['href'] }}" class="drawer-link {{ $active ? 'is-active' : '' }}" style="--i: {{ $loop->index }}"
                       @isset($item['section']) data-section="{{ $item['section'] }}" @endisset>
                        <span class="drawer-link-icon"><x-icon :name="$item['icon']" class="size-[18px]" /></span>
                        <span class="flex-1">{{ $item['label'] }}</span>
                        <x-icon name="chevron-right" class="drawer-link-arrow size-4" />
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto space-y-4 pt-6">
                @if (setting('cv'))
                    <a href="{{ route('cv') }}" class="btn btn-primary w-full"><x-icon name="download" class="size-4" /> Download Resume</a>
                @endif
                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white/60 p-3 dark:border-white/10 dark:bg-white/[0.03]">
                    @include('site.partials.socials', ['size' => 'size-9'])
                    <button type="button" data-theme-toggle class="drawer-theme" aria-label="Toggle dark mode">
                        <x-icon name="sun" class="hidden size-[18px] dark:block" />
                        <x-icon name="moon" class="size-[18px] dark:hidden" />
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 py-10 dark:border-white/[0.07]">
        <div class="container-x flex flex-col items-center justify-between gap-6 sm:flex-row">
            <p class="text-sm text-slate-500">
                {{ setting('footer_text', '© '.date('Y').' '.$name.'. All rights reserved.') }}
            </p>
            @include('site.partials.socials', ['size' => 'size-9'])
        </div>
    </footer>

    {{-- Back to top: appears after scrolling down; the ring fills with reading progress --}}
    <button type="button" class="to-top" data-to-top aria-label="Back to top" tabindex="-1">
        <svg class="to-top-ring" viewBox="0 0 48 48" aria-hidden="true">
            <defs>
                <linearGradient id="to-top-grad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="var(--accent)" /><stop offset="1" stop-color="var(--accent-2)" />
                </linearGradient>
            </defs>
            <circle cx="24" cy="24" r="22" class="to-top-track" />
            <circle cx="24" cy="24" r="22" class="to-top-progress" data-to-top-progress />
        </svg>
        <span class="to-top-arrow"><x-icon name="arrow-up" class="size-5" /></span>
    </button>

    <x-toast-stack />
</body>
</html>
