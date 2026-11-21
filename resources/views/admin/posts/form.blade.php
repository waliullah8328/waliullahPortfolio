@php
    $editing = $post->exists;
    $tools = [
        ['B', '**|**', 'bold text'], ['I', '_|_', 'italic'], ['H2', "\n## |", 'Heading'], ['H3', "\n### |", 'Subheading'],
        ['Link', '[|](https://)', 'link text'], ['Image', '![|](https://image-url)', 'alt text'], ['List', "\n- |", 'item'],
        ['Quote', "\n> |", 'quote'], ['Code', "\n```\n|\n```\n", 'code'],
    ];
@endphp
<x-layouts.admin :title="$editing ? 'Edit post' : 'New post'">
    <form method="POST" action="{{ $editing ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[1fr_340px]">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card space-y-5 p-6">
            <div>
                <label class="label" for="title">Title *</label>
                <input id="title" name="title" value="{{ old('title', $post->title) }}" class="input text-lg font-semibold" required data-slug-source>
            </div>
            <div>
                <label class="label" for="slug">URL slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" class="input" data-slug-target>
            </div>
            <div>
                <label class="label" for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="2" class="input" maxlength="500" placeholder="Short summary for cards and SEO (optional)">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div data-md-editor="{{ route('admin.posts.preview') }}">
                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                    <span class="label mb-0!">Content * <span class="font-normal text-slate-500">(Markdown)</span></span>
                    <div class="flex rounded-lg bg-slate-100 p-1 text-xs font-semibold dark:bg-white/5">
                        <button type="button" data-md-tab="write" class="is-active cursor-pointer rounded-md px-3 py-1 [&.is-active]:bg-white [&.is-active]:shadow dark:[&.is-active]:bg-white/10">Write</button>
                        <button type="button" data-md-tab="preview" class="cursor-pointer rounded-md px-3 py-1 [&.is-active]:bg-white [&.is-active]:shadow dark:[&.is-active]:bg-white/10">Preview</button>
                    </div>
                </div>
                <div class="mb-2 flex flex-wrap gap-1">
                    @foreach ($tools as [$label, $md, $ph])
                        <button type="button" data-md="{{ $md }}" data-placeholder="{{ $ph }}" class="cursor-pointer rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600 hover:border-accent hover:text-accent dark:border-white/10 dark:text-slate-300">{{ $label }}</button>
                    @endforeach
                </div>
                <textarea name="body" rows="22" class="input font-mono text-[13px] leading-6" required>{{ old('body', $post->body) }}</textarea>
                <div data-md-preview class="prose-content hidden min-h-96 rounded-xl border border-slate-200 p-5 dark:border-white/10"></div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card space-y-4 p-6">
                <x-admin.toggle name="is_published" label="Published" help="Off = saved as draft" :checked="old('is_published', $post->is_published)" />
                <div>
                    <label class="label" for="published_at">Publish date</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="input">
                    <p class="help">Set a future date to schedule the post.</p>
                </div>
                <div>
                    <label class="label" for="tags">Tags</label>
                    <input id="tags" name="tags" value="{{ old('tags', $post->tags) }}" class="input" placeholder="laravel, php, tips">
                </div>
            </div>
            <div class="card p-6">
                <x-admin.image-input name="cover_image" label="Cover image" :current="$post->cover_url" :link="$post->cover_image" />
            </div>
            <div class="flex gap-3">
                <button class="btn btn-primary flex-1"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Create post' }}</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
            @if ($editing)
                <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-ghost w-full"><x-icon name="eye" class="size-4" /> View on site</a>
            @endif
        </div>
    </form>
</x-layouts.admin>
