<x-layouts.site title="Projects">
    <section class="relative overflow-hidden pt-32 pb-24">
        <div class="bg-grid absolute inset-0 -z-10"></div>
        <div class="blob -z-10 -top-20 left-1/3 size-96" style="background: var(--accent)"></div>
        <div class="blob -z-10 top-96 -right-40 size-96" style="background: var(--accent-2)"></div>
        <div class="container-x">
            <div class="reveal max-w-2xl">
                <span class="eyebrow"><x-icon name="folder" class="size-3.5" /> Portfolio</span>
                <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">All <span class="gradient-text">projects</span></h1>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">Flutter mobile apps and Laravel web apps I've designed, built and shipped.</p>
            </div>

            <div class="reveal mt-10 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
                <div class="no-scrollbar flex max-w-full shrink-0 gap-1 self-start overflow-x-auto rounded-2xl border border-slate-200 bg-white/70 p-1.5 backdrop-blur dark:border-white/10 dark:bg-white/[0.04]">
                    @foreach (['' => ['All', 'grid'], 'app' => ['Mobile Apps', 'smartphone'], 'website' => ['Websites', 'globe']] as $val => [$lbl, $icon])
                        <a href="{{ route('projects.index', array_filter(['type' => $val, 'category' => request('category')])) }}"
                           class="skill-tab {{ (string) request('type') === $val ? 'is-active' : '' }}">
                            <x-icon :name="$icon" class="size-4" /> {{ $lbl }}
                        </a>
                    @endforeach
                </div>

                @if ($categories->count() > 1)
                    <div class="flex min-w-0 flex-wrap gap-2 lg:justify-end lg:pt-1.5">
                        @foreach (collect(['' => 'All categories'])->merge($categories->mapWithKeys(fn ($c) => [$c => $c])) as $val => $lbl)
                            <a href="{{ route('projects.index', array_filter(['category' => $val, 'type' => request('type')])) }}"
                               class="pc-chip px-3.5! py-1.5! text-xs! transition hover:border-accent hover:text-accent {{ (string) request('category') === (string) $val ? 'border-accent! bg-accent/10! text-accent!' : '' }}">{{ $lbl }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $project)
                    @include('site.partials.project-card')
                @empty
                    <div class="card col-span-full p-12 text-center text-slate-500">No projects found for this filter.</div>
                @endforelse
            </div>
            <div class="mt-12">{{ $projects->links() }}</div>
        </div>
    </section>
</x-layouts.site>
