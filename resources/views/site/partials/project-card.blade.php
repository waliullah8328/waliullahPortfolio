@php $tech = $project->tech_list; @endphp
<article class="project-card reveal group">
    <a href="{{ route('projects.show', $project) }}" class="relative block" aria-label="{{ $project->title }}">
        @include('site.partials.project-media', ['project' => $project])
        <span class="absolute left-3 top-3 z-20 inline-flex items-center gap-1.5 rounded-full bg-white/80 px-2.5 py-1 text-[11px] font-semibold text-slate-800 shadow-sm backdrop-blur dark:bg-black/50 dark:text-white">
            <x-icon :name="$project->is_app ? 'smartphone' : 'globe'" class="size-3 text-accent" /> {{ $project->type_label }}
        </span>
        @if ($project->is_featured)
            <span class="absolute right-3 top-3 z-20 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold text-white shadow-lg shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))">
                <x-icon name="star" class="size-3 fill-current" /> Featured
            </span>
        @endif
    </a>

    <div class="relative z-10 flex flex-1 flex-col p-5 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                @if ($project->category)
                    <p class="text-[11px] font-semibold uppercase tracking-[0.15em] text-accent">{{ $project->category }}</p>
                @endif
                <h3 class="mt-1 text-lg font-semibold leading-snug">
                    <a href="{{ route('projects.show', $project) }}" class="transition group-hover:text-accent">{{ $project->title }}</a>
                </h3>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="pc-arrow" aria-hidden="true" tabindex="-1">
                <x-icon name="arrow-up-right" class="size-4" />
            </a>
        </div>

        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $project->summary }}</p>

        @if ($tech)
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach (array_slice($tech, 0, 4) as $t)
                    <span class="pc-chip">{{ $t }}</span>
                @endforeach
                @if (count($tech) > 4)
                    <span class="pc-chip">+{{ count($tech) - 4 }}</span>
                @endif
            </div>
        @endif

        <div class="h-5 shrink-0"></div>
        <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-200/70 pt-4 dark:border-white/[0.06]">
            <div class="flex h-8 items-center gap-4 text-sm font-medium">
                @if ($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-slate-600 transition hover:text-accent dark:text-slate-300"><x-icon name="external" class="size-4" /> Live</a>
                @endif
                @if ($project->repo_url)
                    <a href="{{ $project->repo_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-slate-600 transition hover:text-accent dark:text-slate-300"><x-icon name="github" class="size-4" /> Code</a>
                @endif
                @if (! $project->live_url && ! $project->repo_url)
                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-accent dark:text-slate-300">View details</a>
                @endif
            </div>
            @if ($project->is_app && ($project->app_store_url || $project->play_store_url))
                <div class="flex items-center gap-1.5">
                    @if ($project->app_store_url)
                        <a href="{{ $project->app_store_url }}" target="_blank" rel="noopener" class="pc-store" title="Download on the App Store" aria-label="App Store"><x-icon name="apple" class="size-4" /></a>
                    @endif
                    @if ($project->play_store_url)
                        <a href="{{ $project->play_store_url }}" target="_blank" rel="noopener" class="pc-store" title="Get it on Google Play" aria-label="Google Play"><x-icon name="google-play" class="size-3.5" /></a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</article>
