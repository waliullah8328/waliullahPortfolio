<x-layouts.site :title="$project->title" :description="$project->summary" :image="$project->image_url">
    <article class="relative overflow-hidden pt-32 pb-24">
        <div class="bg-grid absolute inset-0 -z-10"></div>
        <div class="blob -z-10 -top-20 right-0 size-96" style="background: var(--accent-2)"></div>
        <div class="container-x max-w-4xl">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-accent"><x-icon name="arrow-left" class="size-4" /> All projects</a>

            <header class="reveal mt-8">
                <div class="flex flex-wrap gap-2">
                    <span class="eyebrow"><x-icon :name="$project->is_app ? 'smartphone' : 'globe'" class="size-3.5" /> {{ $project->type_label }}</span>
                    @if ($project->category)<span class="eyebrow">{{ $project->category }}</span>@endif
                </div>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">{{ $project->title }}</h1>
                @if ($project->summary)<p class="mt-5 text-lg leading-relaxed text-slate-600 dark:text-slate-400">{{ $project->summary }}</p>@endif
                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($project->live_url)<a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn btn-primary"><x-icon name="external" class="size-4" /> Live demo</a>@endif
                    @if ($project->repo_url)<a href="{{ $project->repo_url }}" target="_blank" rel="noopener" class="btn btn-ghost"><x-icon name="github" class="size-4" /> Source code</a>@endif
                </div>
                @if ($project->is_app)
                    <div class="mt-4">@include('site.partials.store-badges', ['size' => 'lg'])</div>
                @endif
            </header>

            <div class="reveal group mt-12 overflow-hidden rounded-3xl border border-slate-200 shadow-2xl shadow-accent/10 dark:border-white/10">
                @include('site.partials.project-media', ['project' => $project, 'large' => true])
            </div>

            <div class="mt-12 grid gap-10 lg:grid-cols-[1fr_240px]">
                <div class="prose-content reveal">{!! $project->html !!}</div>
                @if ($project->tech_list)
                    <aside class="reveal">
                        <div class="card sticky top-24 p-5">
                            <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Tech stack</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($project->tech_list as $tech)<span class="chip">{{ $tech }}</span>@endforeach
                            </div>
                        </div>
                    </aside>
                @endif
            </div>
        </div>

        @if ($more->isNotEmpty())
            <div class="container-x mt-24">
                <h2 class="text-2xl font-bold">More projects</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($more as $project)
                        @include('site.partials.project-card')
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</x-layouts.site>
