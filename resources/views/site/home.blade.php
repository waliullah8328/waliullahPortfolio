@php
    $name = setting('name', 'Your Name');
    $avatar = media(setting('avatar'));
    $initials = collect(explode(' ', $name))->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->join('');
    $stats = collect([
        ['value' => setting('years_experience'), 'label' => 'Years experience'],
        ['value' => setting('projects_completed'), 'label' => 'Projects completed'],
        ['value' => setting('happy_clients'), 'label' => 'Happy clients'],
    ])->filter(fn ($s) => filled($s['value']))->values();
@endphp
<x-layouts.site>
    {{-- HERO --}}
    <section class="relative flex min-h-[100svh] items-center overflow-hidden pt-24 pb-16">
        <div class="bg-grid absolute inset-0 -z-10"></div>
        <div class="blob -z-10 -top-24 -left-24 size-[28rem]" style="background: var(--accent)"></div>
        <div class="blob -z-10 top-1/3 -right-32 size-[26rem]" style="background: var(--accent-2)"></div>

        <div class="container-x grid items-center gap-14 lg:grid-cols-[1.2fr_1fr]">
            <div class="reveal">
                @if (setting_on('available_for_work'))
                    <span class="inline-flex items-center gap-2 rounded-full border border-accent-2/40 bg-accent-2/10 px-3 py-1 text-xs font-semibold text-slate-700 dark:text-slate-200">
                        <span class="relative flex size-2"><span class="absolute inline-flex size-full animate-ping rounded-full bg-accent-2 opacity-75"></span><span class="relative inline-flex size-2 rounded-full bg-accent-2"></span></span>
                        Available for new projects
                    </span>
                @endif
                <h1 class="mt-6 text-4xl font-bold leading-[1.1] tracking-tight sm:text-6xl lg:text-7xl">
                    Hi, I'm <span class="gradient-text">{{ $name }}</span>
                </h1>
                <p class="mt-5 font-display text-xl font-medium text-slate-800 sm:text-2xl dark:text-slate-200">
                    {{ setting('role', 'Developer') }}
                    @if (setting('typed_words'))
                        <span class="text-slate-400">·</span> <span class="caret text-accent" data-typed="{{ setting('typed_words') }}"></span>
                    @endif
                </p>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600 dark:text-slate-400">{{ setting('hero_intro') }}</p>

                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="#projects" class="btn btn-primary">View my work <x-icon name="arrow-right" class="size-4" /></a>
                    @if (setting_on('show_contact'))
                        <a href="#contact" class="btn btn-ghost">Let's talk</a>
                    @endif
                    @if (setting('cv'))
                        <a href="{{ route('cv') }}" class="btn btn-ghost"><x-icon name="download" class="size-4" /> Download CV</a>
                    @endif
                </div>
                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-5">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">One codebase, every platform</p>
                        <div class="mt-2.5 flex items-center gap-2">
                            @foreach (['apple' => 'iOS', 'android' => 'Android', 'globe' => 'Web', 'monitor' => 'Desktop'] as $icon => $label)
                                <span class="grid size-10 place-items-center rounded-xl border border-slate-200 bg-white/70 text-slate-700 backdrop-blur dark:border-white/10 dark:bg-white/5 dark:text-slate-200" title="{{ $label }}">
                                    <x-icon :name="$icon" class="size-[18px]" />
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <span class="hidden h-12 w-px bg-slate-200 sm:block dark:bg-white/10"></span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Find me on</p>
                        <div class="mt-2.5">@include('site.partials.socials')</div>
                    </div>
                </div>
            </div>

            @php
                $heroTech = $skillGroups->flatten()->filter(fn ($s) => $s->icon)->sortByDesc("level")->take(3)->values();
                $heroApps = collect([$spotlight])->filter()->merge($projects)->take(3);
            @endphp
            <div class="reveal relative mx-auto grid h-[36rem] w-full max-w-[26rem] place-items-center sm:h-[38rem]">
                <div class="hero-orbit size-[22rem] sm:size-[26rem]"></div>
                <div class="hero-orbit hero-orbit-rev size-[17rem] sm:size-[20rem]"></div>
                <div class="absolute size-72 rounded-full opacity-50 blur-3xl" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"></div>

                {{-- Phone --}}
                <div class="hero-phone">
                    <span class="hero-island"></span>
                    <div class="hero-screen">
                        <div class="flex items-center justify-between px-5 pt-3 text-[10px] font-semibold text-slate-900 dark:text-white">
                            <span>9:41</span>
                            <span class="flex items-center gap-1"><i class="pm-bar w-3 bg-current! opacity-80"></i><i class="pm-bar w-4 bg-current! opacity-80"></i></span>
                        </div>
                        <div class="relative mx-3 mt-5 overflow-hidden rounded-2xl p-4 text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">
                            <div class="absolute -right-6 -top-6 size-20 rounded-full bg-white/15"></div>
                            <div class="relative flex items-center gap-3">
                                @if ($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $name }}" class="size-12 rounded-full object-cover ring-2 ring-white/60">
                                @else
                                    <span class="grid size-12 place-items-center rounded-full bg-white/20 font-display font-bold ring-2 ring-white/60">{{ $initials }}</span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-display text-sm font-bold">{{ $name }}</p>
                                    <p class="truncate text-[10px] text-white/80">{{ setting('role', 'Developer') }}</p>
                                </div>
                            </div>
                            @if ($stats->isNotEmpty())
                                <div class="relative mt-4 grid gap-1 rounded-xl bg-black/15 p-2 text-center" style="grid-template-columns: repeat({{ $stats->count() }}, minmax(0, 1fr))">
                                    @foreach ($stats as $s)
                                        <div>
                                            <p class="font-display text-sm font-bold">{{ $s['value'] }}</p>
                                            <p class="text-[8px] uppercase tracking-wide text-white/75">{{ \Illuminate\Support\Str::of($s['label'])->explode(' ')->last() }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <p class="mx-4 mt-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">Recent apps</p>
                        <div class="mx-3 mt-2 space-y-2">
                            @foreach ($heroApps as $app)
                                <div class="flex items-center gap-2.5 rounded-xl border border-slate-100 bg-slate-50 p-2 dark:border-white/5 dark:bg-white/[0.04]">
                                    <span class="grid size-8 shrink-0 place-items-center rounded-lg text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"><x-icon :name="$app->is_app ? 'smartphone' : 'globe'" class="size-4" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[11px] font-semibold text-slate-900 dark:text-white">{{ $app->title }}</p>
                                        <p class="truncate text-[9px] text-slate-500">{{ $app->category ?: $app->type_label }}</p>
                                    </div>
                                    <x-icon name="arrow-up-right" class="size-3 shrink-0 text-slate-400" />
                                </div>
                            @endforeach
                        </div>
                        <div class="absolute inset-x-3 bottom-6">
                            <div class="grid h-9 place-items-center rounded-xl text-[11px] font-bold text-white shadow-lg shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">Hire me</div>
                        </div>
                        <span class="absolute bottom-2 left-1/2 h-1 w-20 -translate-x-1/2 rounded-full bg-slate-900/80 dark:bg-white/60"></span>
                    </div>
                </div>

                {{-- Floating tech badges (first 3 skills with an icon) --}}
                @foreach ($heroTech as $i => $tech)
                    <div class="float-chip animate-float {{ ['left-0 top-[16%]', 'right-0 top-[40%] [animation-delay:1.2s]', 'left-2 bottom-[18%] [animation-delay:2.4s]'][$i] }}">
                        <img src="{{ $tech->icon }}" alt="" class="size-6">
                        <span>{{ $tech->name }}</span>
                    </div>
                @endforeach
                @if ($stats->count() > 1)
                    <div class="float-chip animate-float right-0 top-[63%] hidden! flex-col items-start! gap-0! [animation-delay:3s] sm:flex!">
                        <span class="font-display text-xl font-bold gradient-text">{{ $stats[1]['value'] ?? $stats->last()['value'] }}</span>
                        <span class="text-[10px] font-medium text-slate-500">{{ $stats[1]['label'] ?? $stats->last()['label'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    @if (setting_on('show_about'))
    <section id="about" class="section scroll-mt-16">
        <div class="container-x grid items-center gap-14 lg:grid-cols-2">
            <div class="reveal relative order-2 lg:order-1">
                @if ($img = media(setting('about_image')) ?? $avatar)
                    <img src="{{ $img }}" alt="About {{ $name }}" class="aspect-square w-full rounded-3xl object-cover shadow-xl">
                @else
                    <div class="card grid aspect-square place-items-center p-10">
                        <x-icon name="sparkles" class="size-24 text-accent/60" />
                    </div>
                @endif
                @if ($stats->count() > 1)
                    <div class="absolute -bottom-8 inset-x-6 grid gap-2 rounded-2xl border border-slate-200 bg-white/95 p-4 text-center shadow-xl backdrop-blur dark:border-white/10 dark:bg-slate-900/95" style="grid-template-columns: repeat({{ $stats->count() }}, minmax(0, 1fr))">
                        @foreach ($stats as $s)
                            <div>
                                <p class="font-display text-2xl font-bold text-slate-900 sm:text-3xl dark:text-white" data-count="{{ $s['value'] }}">0</p>
                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">{{ $s['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="reveal order-1 lg:order-2">
                <span class="eyebrow">About me</span>
                <h2 class="section-title">{{ setting('about_title', 'Turning ideas into delightful digital products') }}</h2>
                <div class="prose-content mt-6">{!! Str::markdown(setting('about', ''), ['html_input' => 'strip']) !!}</div>
                <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach (['email' => 'mail', 'phone' => 'phone', 'location' => 'map-pin'] as $key => $icon)
                        @if (setting($key))
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-xl bg-accent/10 text-accent"><x-icon :name="$icon" class="size-4" /></span>
                                <div class="min-w-0">
                                    <dt class="text-xs uppercase tracking-wide text-slate-500">{{ ucfirst($key) }}</dt>
                                    <dd class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ setting($key) }}</dd>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
        </div>
    </section>
    @endif

    {{-- SERVICES --}}
    @if (setting_on('show_services') && $services->isNotEmpty())
    <section id="services" class="section relative scroll-mt-16 overflow-hidden bg-white/50 dark:bg-white/[0.015]">
        <div class="blob -z-10 -top-20 left-1/3 size-[26rem]" style="background: var(--accent)"></div>
        <div class="container-x">
            <div class="reveal flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                <div class="max-w-xl">
                    <span class="eyebrow"><x-icon name="sparkles" class="size-3.5" /> What I do</span>
                    <h2 class="section-title">From idea to <span class="gradient-text">App Store</span></h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">End-to-end mobile app development: design, build, test and release, all with one Flutter codebase.</p>
                </div>
                @if (setting_on('show_contact'))
                    <a href="#contact" class="btn btn-primary shrink-0">Start a project <x-icon name="arrow-right" class="size-4" /></a>
                @endif
            </div>

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <div class="skill-tile reveal flex flex-col p-6! sm:p-7!">
                        <div class="relative z-10 flex items-start justify-between">
                            <span class="service-icon"><x-icon :name="$service->icon" class="size-6" /></span>
                            <span class="font-display text-4xl font-bold text-slate-200 transition-colors duration-300 dark:text-white/[0.07]">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="relative z-10 mt-6 text-lg font-semibold">{{ $service->title }}</h3>
                        <p class="relative z-10 mt-2 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $service->description }}</p>
                        @if ($service->tag_list)
                            <div class="relative z-10 mt-5 flex flex-wrap gap-1.5">
                                @foreach ($service->tag_list as $tag)<span class="pc-chip">{{ $tag }}</span>@endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- SKILLS --}}
    @if (setting_on('show_skills') && $skillGroups->isNotEmpty())
    @php
        $allSkills = $skillGroups->flatten();
        $levelName = fn ($l) => $l >= 90 ? 'Expert' : ($l >= 75 ? 'Advanced' : ($l >= 50 ? 'Intermediate' : 'Learning'));
        $ring = 2 * M_PI * 22; // circumference for r = 22
        $half = (int) ceil($allSkills->count() / 2);
        $rows = $allSkills->count() > 6 ? [$allSkills->take($half), $allSkills->skip($half)] : [$allSkills];
    @endphp
    <section id="skills" class="section relative scroll-mt-16 overflow-hidden">
        <div class="bg-grid absolute inset-0 -z-10 opacity-60"></div>
        <div class="blob -z-10 top-10 -left-40 size-[30rem]" style="background: var(--accent-2)"></div>
        <div class="blob -z-10 bottom-0 -right-40 size-[26rem]" style="background: var(--accent)"></div>

        {{-- shared gradient for the progress rings --}}
        <svg width="0" height="0" class="absolute" aria-hidden="true">
            <defs>
                <linearGradient id="skill-ring-grad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" style="stop-color: var(--accent)" />
                    <stop offset="100%" style="stop-color: var(--accent-2)" />
                </linearGradient>
            </defs>
        </svg>

        <div class="container-x">
            <div class="reveal flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <div class="max-w-xl">
                    <span class="eyebrow"><x-icon name="layers" class="size-3.5" /> Skills</span>
                    <h2 class="section-title">My <span class="gradient-text">toolbox</span></h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">The languages, frameworks and tools I use every day to design, build and ship products.</p>
                </div>
                <dl class="grid grid-cols-3 gap-3 sm:gap-4">
                    @foreach ([
                        [$allSkills->count(), 'Technologies'],
                        [$skillGroups->count(), 'Categories'],
                        [round($allSkills->avg('level')).'%', 'Avg. level'],
                    ] as [$val, $lbl])
                        <div class="rounded-2xl border border-slate-200 bg-white/70 px-4 py-3 text-center backdrop-blur sm:px-6 dark:border-white/10 dark:bg-white/[0.04]">
                            <dd class="font-display text-2xl font-bold text-slate-900 sm:text-3xl dark:text-white" data-count="{{ $val }}">0</dd>
                            <dt class="text-[11px] font-medium uppercase tracking-wider text-slate-500">{{ $lbl }}</dt>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if ($skillGroups->count() > 1)
                <div class="reveal mt-10 flex gap-1 overflow-x-auto rounded-2xl border border-slate-200 bg-white/70 p-1.5 backdrop-blur sm:inline-flex dark:border-white/10 dark:bg-white/[0.04]" data-skill-tabs role="tablist">
                    <button type="button" data-skill-tab="*" class="skill-tab is-active">All <span>{{ $allSkills->count() }}</span></button>
                    @foreach ($skillGroups as $category => $skills)
                        <button type="button" data-skill-tab="{{ $category }}" class="skill-tab">{{ $category }} <span>{{ $skills->count() }}</span></button>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5" data-skill-grid>
                @foreach ($allSkills as $skill)
                    <div class="skill-tile reveal" data-skill-cat="{{ $skill->category }}">
                        <div class="relative z-10 flex items-start justify-between gap-2">
                            <span class="skill-icon">
                                @if ($skill->icon)
                                    <img src="{{ $skill->icon }}" alt="" class="size-8 sm:size-9" loading="lazy">
                                @else
                                    <span class="gradient-text font-display text-lg font-bold">{{ mb_strtoupper(mb_substr($skill->name, 0, 2)) }}</span>
                                @endif
                            </span>
                            <span class="relative grid size-14 shrink-0 place-items-center">
                                <svg viewBox="0 0 52 52" class="absolute inset-0 -rotate-90">
                                    <circle cx="26" cy="26" r="22" fill="none" stroke-width="4" class="stroke-slate-200 dark:stroke-white/10" />
                                    <circle cx="26" cy="26" r="22" fill="none" stroke-width="4" stroke-linecap="round" stroke="url(#skill-ring-grad)"
                                            class="skill-ring" stroke-dasharray="{{ $ring }}" stroke-dashoffset="{{ $ring }}"
                                            data-ring="{{ $ring * (1 - $skill->level / 100) }}" />
                                </svg>
                                <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $skill->level }}<span class="text-[9px] text-slate-400">%</span></span>
                            </span>
                        </div>
                        <div class="relative z-10 mt-5">
                            <h3 class="truncate font-display text-base font-semibold sm:text-lg">{{ $skill->name }}</h3>
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                                <span class="font-semibold text-accent">{{ $levelName($skill->level) }}</span>
                                <span class="size-1 rounded-full bg-slate-300 dark:bg-white/20"></span>
                                <span class="truncate text-slate-500">{{ $skill->category }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        {{-- Logo ticker (inside the container, same width as other sections) --}}
        <div class="reveal mt-12 space-y-4">
            @foreach ($rows as $r => $row)
                <div class="marquee overflow-hidden">
                    <div class="marquee-track {{ $r === 1 ? 'marquee-reverse' : '' }}">
                        @foreach ([1, 2] as $copy)
                            @foreach ($row as $skill)
                                <span class="mr-4 inline-flex items-center gap-3 whitespace-nowrap rounded-2xl border border-slate-200 bg-white/80 py-2.5 pl-2.5 pr-5 text-sm font-semibold text-slate-700 backdrop-blur dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-200" @if($copy === 2) aria-hidden="true" @endif>
                                    <span class="grid size-8 place-items-center rounded-xl bg-slate-100 dark:bg-white/5">
                                        @if ($skill->icon)<img src="{{ $skill->icon }}" alt="" class="size-5" loading="lazy">@else<span class="text-[10px] font-bold text-accent">{{ mb_substr($skill->name, 0, 2) }}</span>@endif
                                    </span>
                                    {{ $skill->name }}
                                </span>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </section>
    @endif

    {{-- EXPERIENCE --}}
    @if (setting_on('show_experience') && $experiences->isNotEmpty())
    @php
        $workItems = $experiences['work'] ?? collect();
        $eduItems = $experiences['education'] ?? collect();
        $workMonths = $workItems->sum(fn ($e) => $e->months);
        $groups = [
            'work' => ['Work experience', 'briefcase', $workItems->count().' '.str('role')->plural($workItems->count())],
            'education' => ['Education', 'graduation', $eduItems->count().' '.str('qualification')->plural($eduItems->count())],
        ];
    @endphp
    <section id="experience" class="section relative scroll-mt-16 overflow-hidden">
        <div class="blob -z-10 -left-40 top-1/3 size-96 opacity-25 dark:opacity-50" style="background: var(--accent)"></div>
        <div class="blob -z-10 -right-40 bottom-0 size-96 opacity-20 dark:opacity-40" style="background: var(--accent-2)"></div>
        <div class="container-x">
            <div class="reveal mx-auto max-w-2xl text-center">
                <span class="eyebrow"><x-icon name="award" class="size-3.5" /> Journey</span>
                <h2 class="section-title">Experience <span class="gradient-text">&amp; Education</span></h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400">Where I've worked and what I've studied along the way.</p>
            </div>

            <div class="mt-16 grid gap-14 {{ $experiences->count() > 1 ? 'lg:grid-cols-2 lg:gap-10' : 'mx-auto max-w-3xl' }}">
                @foreach ($groups as $type => [$label, $icon, $count])
                    @if ($experiences->has($type))
                        <div>
                            <div class="reveal mb-8 flex items-center justify-between gap-4">
                                <h3 class="flex items-center gap-3 text-xl font-semibold">
                                    <span class="xp-head-icon"><x-icon :name="$icon" class="size-5" /></span>
                                    <span>{{ $label }}<span class="block text-xs font-medium text-slate-500">{{ $count }}</span></span>
                                </h3>
                                @if ($type === 'work' && $workMonths >= 1)
                                    <span class="xp-total"><x-icon name="clock" class="size-3.5" /> {{ \App\Models\Experience::humanMonths($workMonths) }}</span>
                                @endif
                            </div>

                            <ol class="xp-line">
                                @foreach ($experiences[$type] as $exp)
                                    <li class="reveal xp-item">
                                        <span class="xp-node {{ $exp->is_current ? 'is-current' : '' }}"><x-icon :name="$icon" class="size-3.5" /></span>
                                        <article class="xp-card">
                                            <div class="flex items-start gap-4">
                                                <span class="xp-logo" aria-hidden="true">{{ $exp->initials }}</span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="xp-period"><x-icon name="calendar" class="size-3" /> {{ $exp->period }}</span>
                                                        @if ($exp->is_current)
                                                            <span class="xp-current"><i></i> Current</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="xp-title mt-2.5 text-lg font-semibold leading-snug">{{ $exp->title }}</h4>
                                                    <p class="mt-0.5 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $exp->organization }}</p>
                                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                                        @if ($exp->location)<span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="size-3.5" /> {{ $exp->location }}</span>@endif
                                                        @if ($exp->months >= 1)<span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-3.5" /> {{ \App\Models\Experience::humanMonths($exp->months) }}</span>@endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($exp->points)
                                                <ul class="mt-5 space-y-2.5 border-t border-slate-200/70 pt-4 text-sm leading-relaxed text-slate-600 dark:border-white/[0.06] dark:text-slate-300">
                                                    @foreach ($exp->points as $point)
                                                        <li class="xp-point" style="--i: {{ $loop->index }}"><span class="xp-tick" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 12.5l4 4 8-9"/></svg></span><span>{{ $point }}</span></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </article>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- PROJECTS --}}
    @if (setting_on('show_projects') && ($spotlight || $projects->isNotEmpty()))
    <section id="projects" class="section relative scroll-mt-16 overflow-hidden bg-white/50 dark:bg-white/[0.015]">
        <div class="blob -z-10 -top-32 right-0 size-[28rem]" style="background: var(--accent)"></div>
        <div class="container-x">
            <div class="reveal flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                <div class="max-w-xl">
                    <span class="eyebrow"><x-icon name="folder" class="size-3.5" /> Portfolio</span>
                    <h2 class="section-title">Selected <span class="gradient-text">work</span></h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">Flutter apps for iOS and Android, plus the Laravel web apps and backends behind them.</p>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-ghost shrink-0">All projects <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            {{-- Spotlight --}}
            @if ($spotlight)
                @php $p = $spotlight; @endphp
                <article class="project-card reveal group mt-12 lg:flex-row!">
                    <a href="{{ route('projects.show', $p) }}" class="pm-fill relative block lg:w-[55%] lg:shrink-0" aria-label="{{ $p->title }}">
                        @include('site.partials.project-media', ['project' => $p, 'large' => true])
                    </a>
                    <div class="relative z-10 flex flex-1 flex-col justify-center p-6 sm:p-8 lg:p-10">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"><x-icon name="star" class="size-3 fill-current" /> Featured</span>
                            <span class="pc-chip"><x-icon :name="$p->is_app ? 'smartphone' : 'globe'" class="mr-1 size-3 text-accent" /> {{ $p->type_label }}</span>
                            @if ($p->category && strcasecmp($p->category, $p->type_label) !== 0)<span class="pc-chip">{{ $p->category }}</span>@endif
                        </div>
                        <h3 class="mt-5 text-2xl font-bold leading-tight sm:text-3xl">
                            <a href="{{ route('projects.show', $p) }}" class="transition group-hover:text-accent">{{ $p->title }}</a>
                        </h3>
                        <p class="mt-3 leading-relaxed text-slate-600 dark:text-slate-400">{{ $p->summary }}</p>
                        @if ($p->tech_list)
                            <div class="mt-5 flex flex-wrap gap-1.5">
                                @foreach ($p->tech_list as $t)<span class="pc-chip">{{ $t }}</span>@endforeach
                            </div>
                        @endif
                        <div class="mt-7 flex flex-wrap items-center gap-2">
                            <a href="{{ route('projects.show', $p) }}" class="btn btn-primary">View case study <x-icon name="arrow-right" class="size-4" /></a>
                            @if ($p->live_url)<a href="{{ $p->live_url }}" target="_blank" rel="noopener" class="btn btn-ghost px-4"><x-icon name="external" class="size-4" /> Live</a>@endif
                            @if ($p->repo_url)<a href="{{ $p->repo_url }}" target="_blank" rel="noopener" class="btn btn-ghost px-4"><x-icon name="github" class="size-4" /> Code</a>@endif
                        </div>
                        @if ($p->is_app)
                            <div class="mt-4">@include('site.partials.store-badges', ['project' => $p, 'size' => 'sm'])</div>
                        @endif
                    </div>
                </article>
            @endif

            @if ($projects->isNotEmpty())
                @php
                    $platformsOf = fn ($p) => match ($p->type) { 'both' => 'app|web', 'website' => 'web', default => 'app' };
                    $appCount = $projects->filter(fn ($p) => $p->is_app)->count();
                    $webCount = $projects->filter(fn ($p) => in_array($p->type, ['website', 'both'], true))->count();
                @endphp
                @if ($appCount && $webCount)
                    <div class="reveal mt-14 flex gap-1 overflow-x-auto rounded-2xl border border-slate-200 bg-white/70 p-1.5 backdrop-blur sm:inline-flex dark:border-white/10 dark:bg-white/[0.04]" data-filter-group="home">
                        <button type="button" data-filter="*" class="skill-tab is-active"><x-icon name="grid" class="size-4" /> All <span>{{ $projects->count() }}</span></button>
                        <button type="button" data-filter="app" class="skill-tab"><x-icon name="smartphone" class="size-4" /> Mobile Apps <span>{{ $appCount }}</span></button>
                        <button type="button" data-filter="web" class="skill-tab"><x-icon name="globe" class="size-4" /> Web <span>{{ $webCount }}</span></button>
                    </div>
                @endif
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <div data-filter-item="home" data-category="{{ $platformsOf($project) }}" class="h-full">
                            @include('site.partials.project-card')
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- RECOMMENDATIONS --}}
    @if (setting_on('show_testimonials'))
    <section id="testimonials" class="section scroll-mt-16">
        <div class="container-x">
            <div class="reveal mx-auto max-w-2xl text-center">
                <span class="eyebrow">Recommendations</span>
                <h2 class="section-title">What people say</h2>
            </div>

            @if ($testimonials->isNotEmpty())
                <div class="mt-14 columns-1 gap-6 space-y-6 md:columns-2 lg:columns-3">
                    @foreach ($testimonials as $t)
                        <figure class="card reveal break-inside-avoid p-7">
                            <x-icon name="quote" class="size-8 text-accent/30" />
                            <div class="mt-3 flex gap-0.5 text-accent">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-icon name="star" class="size-4 {{ $i <= $t->rating ? 'fill-current' : 'text-slate-300 dark:text-white/15' }}" />
                                @endfor
                            </div>
                            <blockquote class="mt-4 leading-relaxed text-slate-700 dark:text-slate-300">“{{ $t->content }}”</blockquote>
                            <figcaption class="mt-6 flex items-center gap-3">
                                @if ($t->avatar_url)
                                    <img src="{{ $t->avatar_url }}" alt="{{ $t->name }}" class="size-11 rounded-full object-cover">
                                @else
                                    <span class="grid size-11 place-items-center rounded-full text-sm font-bold text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">{{ $t->initials }}</span>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $t->name }}</p>
                                    <p class="text-sm text-slate-500">{{ collect([$t->position, $t->company])->filter()->join(' · ') }}</p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif

            @if (setting_on('allow_recommendations'))
                <div class="reveal mx-auto mt-12 max-w-2xl">
                    <details class="card group p-6" @if($errors->recommend->any()) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-900 dark:text-white">
                            <span class="flex items-center gap-2"><x-icon name="award" class="text-accent" /> Worked with me? Leave a recommendation</span>
                            <x-icon name="plus" class="size-5 transition group-open:rotate-45" />
                        </summary>
                        <form method="POST" action="{{ route('recommend.store') }}" class="mt-6 grid gap-4 sm:grid-cols-2" data-ajax-form data-success-title="Thank you!">
                            @csrf
                            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                            <div><label class="label" for="r-name">Name *</label><input id="r-name" name="name" value="{{ old('name') }}" class="input" required></div>
                            <div><label class="label" for="r-position">Position</label><input id="r-position" name="position" value="{{ old('position') }}" class="input"></div>
                            <div class="sm:col-span-2"><label class="label" for="r-company">Company</label><input id="r-company" name="company" value="{{ old('company') }}" class="input"></div>
                            <div class="sm:col-span-2">
                                <span class="label">Rating</span>
                                <div class="flex gap-1 text-slate-300 dark:text-white/20" data-rating>
                                    <input type="hidden" name="rating" value="{{ old('rating', 5) }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" class="cursor-pointer transition hover:scale-110" aria-label="{{ $i }} stars"><x-icon name="star" class="size-7 fill-current" /></button>
                                    @endfor
                                </div>
                            </div>
                            <div class="sm:col-span-2"><label class="label" for="r-content">Recommendation *</label><textarea id="r-content" name="content" rows="4" class="input" required>{{ old('content') }}</textarea></div>
                            @if ($errors->recommend->any())
                                <p class="error sm:col-span-2">{{ $errors->recommend->first() }}</p>
                            @endif
                            <div class="sm:col-span-2"><button class="btn btn-primary"><x-icon name="send" class="size-4" /> Submit recommendation</button></div>
                        </form>
                    </details>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- BLOG --}}
    @if (setting_on('show_blog') && $posts->isNotEmpty())
    <section id="blog" class="section scroll-mt-16 bg-white/50 dark:bg-white/[0.015]">
        <div class="container-x">
            <div class="reveal flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <span class="eyebrow">Blog</span>
                    <h2 class="section-title">Latest writing</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost">All articles <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    @include('site.partials.post-card')
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CONTACT --}}
    @if (setting_on('show_contact'))
    <section id="contact" class="section scroll-mt-16">
        <div class="container-x">
            <div class="card reveal relative overflow-hidden p-6 sm:p-12">
                <div class="blob -right-20 -top-20 size-72" style="background: var(--accent)"></div>
                <div class="relative grid gap-12 lg:grid-cols-[1fr_1.3fr]">
                    <div>
                        <span class="eyebrow">Contact</span>
                        <h2 class="section-title">Have a project or a question?</h2>
                        <p class="mt-4 text-slate-600 dark:text-slate-400">Send me a message and I'll reply as soon as possible. Every query is welcome.</p>
                        <div class="mt-8 space-y-4">
                            @foreach (['email' => 'mail', 'phone' => 'phone', 'location' => 'map-pin'] as $key => $icon)
                                @if (setting($key))
                                    @php $href = match ($key) { 'email' => 'mailto:'.setting($key), 'phone' => 'tel:'.preg_replace('/\s+/', '', setting($key)), default => null }; @endphp
                                    <a @if($href) href="{{ $href }}" @endif class="flex items-center gap-4 rounded-xl p-2 -m-2 transition hover:bg-accent/5">
                                        <span class="grid size-12 place-items-center rounded-xl text-white" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"><x-icon :name="$icon" /></span>
                                        <span class="font-medium text-slate-900 dark:text-white">{{ setting($key) }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-8">@include('site.partials.socials')</div>
                    </div>

                    <form method="POST" action="{{ route('contact.store') }}" class="grid gap-4 sm:grid-cols-2" data-ajax-form data-success-title="Message sent">
                        @csrf
                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                        <div>
                            <label class="label" for="c-name">Name *</label>
                            <input id="c-name" name="name" value="{{ old('name') }}" class="input" required placeholder="Jane Doe">
                            @error('name')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="c-email">Email *</label>
                            <input id="c-email" type="email" name="email" value="{{ old('email') }}" class="input" required placeholder="jane@example.com">
                            @error('email')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="c-subject">Subject</label>
                            <input id="c-subject" name="subject" value="{{ old('subject') }}" class="input" placeholder="Project inquiry">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="c-message">Message *</label>
                            <textarea id="c-message" name="message" rows="5" class="input" required placeholder="Tell me about your project...">{{ old('message') }}</textarea>
                            @error('message')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <button class="btn btn-primary w-full sm:w-auto"><x-icon name="send" class="size-4" /> Send message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @endif
</x-layouts.site>
