<x-layouts.site title="Blog">
    <section class="relative overflow-hidden pt-32 pb-24">
        <div class="bg-grid absolute inset-0 -z-10"></div>
        <div class="blob -z-10 -top-20 left-1/4 size-96" style="background: var(--accent)"></div>
        <div class="container-x">
            <div class="reveal flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div class="max-w-2xl">
                    <span class="eyebrow">Blog</span>
                    <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Thoughts & tutorials</h1>
                    <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">Notes on development, design and things I learn along the way.</p>
                </div>
                <form method="GET" class="relative w-full md:w-72">
                    <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search articles..." class="input pl-10">
                </form>
            </div>

            @if (request('tag') || request('q'))
                <p class="mt-8 text-sm text-slate-500">
                    Showing results for <strong class="text-slate-900 dark:text-white">{{ request('tag') ?: request('q') }}</strong>
                    · <a href="{{ route('blog.index') }}" class="text-accent">clear</a>
                </p>
            @endif

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    @include('site.partials.post-card')
                @empty
                    <p class="text-slate-500">No articles found.</p>
                @endforelse
            </div>
            <div class="mt-12">{{ $posts->links() }}</div>
        </div>
    </section>
</x-layouts.site>
