<x-layouts.site :title="$post->title" :description="$post->display_excerpt" :image="$post->cover_url">
    <article class="relative overflow-hidden pt-32 pb-24">
        <div class="bg-grid absolute inset-0 -z-10 h-[32rem]"></div>
        <div class="container-x max-w-3xl">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-accent"><x-icon name="arrow-left" class="size-4" /> All articles</a>

            <header class="reveal mt-8">
                @if (! $post->is_published)
                    <span class="mb-4 inline-block rounded-full bg-accent/15 px-3 py-1 text-xs font-semibold text-accent">Draft preview</span>
                @endif
                <h1 class="text-4xl font-bold leading-tight tracking-tight sm:text-5xl">{{ $post->title }}</h1>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                    <span class="flex items-center gap-2">
                        @if (setting('avatar'))<img src="{{ media(setting('avatar')) }}" alt="" class="size-8 rounded-full object-cover">@endif
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ setting('name') }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="size-4" />{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-4" />{{ $post->reading_time }} min read</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="eye" class="size-4" />{{ number_format($post->views) }} views</span>
                </div>
            </header>

            @if ($post->cover_url)
                <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" class="reveal mt-10 w-full rounded-3xl shadow-xl">
            @endif

            <div class="prose-content mt-10 text-[1.05rem]">{!! $post->html !!}</div>

            @if ($post->tag_list)
                <div class="mt-12 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-8 dark:border-white/10">
                    <x-icon name="tag" class="size-4 text-slate-400" />
                    @foreach ($post->tag_list as $tag)
                        <a href="{{ route('blog.index', ['tag' => $tag]) }}" class="chip hover:bg-accent/10 hover:text-accent">#{{ $tag }}</a>
                    @endforeach
                </div>
            @endif

            <div class="card mt-12 flex flex-col items-start gap-4 p-6 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <p class="font-semibold text-slate-900 dark:text-white">Enjoyed this article?</p>
                    <p class="text-sm text-slate-500">Have a question or want to work together? I'd love to hear from you.</p>
                </div>
                <a href="{{ route('home') }}#contact" class="btn btn-primary">Get in touch</a>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="container-x mt-24">
                <h2 class="text-2xl font-bold">Keep reading</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($related as $post)
                        @include('site.partials.post-card')
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</x-layouts.site>
