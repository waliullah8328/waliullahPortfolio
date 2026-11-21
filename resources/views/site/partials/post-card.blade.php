<article class="card card-hover reveal group flex h-full flex-col overflow-hidden">
    <a href="{{ route('blog.show', $post) }}" class="block aspect-[16/9] overflow-hidden bg-slate-100 dark:bg-white/5">
        @if ($post->cover_url)
            <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="relative grid size-full place-items-center overflow-hidden" style="background-image: linear-gradient(135deg, color-mix(in oklab, var(--accent) 25%, transparent), color-mix(in oklab, var(--accent-2) 25%, transparent))">
                <x-icon name="file-text" class="size-12 text-accent/70" />
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-center gap-3 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="size-3.5" />{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
            <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-3.5" />{{ $post->reading_time }} min read</span>
        </div>
        <h3 class="mt-3 text-lg font-semibold leading-snug">
            <a href="{{ route('blog.show', $post) }}" class="transition hover:text-accent">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 line-clamp-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $post->display_excerpt }}</p>
        <a href="{{ route('blog.show', $post) }}" class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-accent">Read article <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" /></a>
    </div>
</article>
