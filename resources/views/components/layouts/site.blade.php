@props(['title' => null, 'description' => null, 'image' => null])
@php
    $name = setting('name', 'Your Name');
    $theme = setting('default_theme', 'dark');
    $home = request()->routeIs('home') ? '' : route('home');
    $nav = collect([
        ['label' => 'About', 'href' => $home.'#about', 'on' => setting_on('show_about')],
        ['label' => 'Services', 'href' => $home.'#services', 'on' => setting_on('show_services')],
        ['label' => 'Skills', 'href' => $home.'#skills', 'on' => setting_on('show_skills')],
        ['label' => 'Experience', 'href' => $home.'#experience', 'on' => setting_on('show_experience')],
        ['label' => 'Certificates', 'href' => $home.'#certificates', 'on' => setting_on('show_certificates') && \App\Models\Certificate::visible()->exists()],
        ['label' => 'Projects', 'href' => route('projects.index'), 'on' => setting_on('show_projects')],
        ['label' => 'Blog', 'href' => route('blog.index'), 'on' => setting_on('show_blog')],
        ['label' => 'Contact', 'href' => $home.'#contact', 'on' => setting_on('show_contact')],
    ])->where('on', true);
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
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] btn btn-primary">Skip to content</a>

    <header data-header class="fixed inset-x-0 top-0 z-50 backdrop-blur-xl transition-all duration-300">
        <nav class="container-x flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-display text-lg font-bold text-slate-900 dark:text-white">
                <span class="grid size-9 place-items-center rounded-xl text-sm text-white shadow-lg shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">
                    {{ collect(explode(' ', $name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}
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
                <button type="button" data-toggle="mobile-nav" aria-expanded="false" aria-label="Open menu" class="grid size-10 place-items-center rounded-xl text-slate-700 hover:bg-slate-200/60 md:hidden dark:text-slate-200 dark:hover:bg-white/10">
                    <x-icon name="menu" />
                </button>
            </div>
        </nav>
        <div id="mobile-nav" data-close-on-click class="hidden border-t border-slate-200 bg-white/95 px-4 py-3 md:hidden dark:border-white/10 dark:bg-[#07070d]/95">
            @foreach ($nav as $item)
                <a href="{{ $item['href'] }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-white/5">{{ $item['label'] }}</a>
            @endforeach
            @if (setting('cv'))
                <a href="{{ route('cv') }}" class="btn btn-primary mt-2 w-full"><x-icon name="download" class="size-4" /> Download Resume</a>
            @endif
        </div>
    </header>

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

    <x-toast-stack />
</body>
</html>
