<x-layouts.admin title="Blog posts">
    <x-slot:actions>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">New post</span></a>
    </x-slot:actions>

    <form method="GET" class="relative mb-6 max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search posts..." class="input pl-10">
    </form>

    @if ($posts->isEmpty())
        <x-admin.empty icon="file-text" title="No posts yet" :action="route('admin.posts.create')" action-label="Write your first post">Share tutorials, case studies and thoughts.</x-admin.empty>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead><tr><th>Post</th><th class="hidden md:table-cell">Status</th><th class="hidden md:table-cell">Views</th><th class="hidden lg:table-cell">Date</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="size-12 shrink-0 overflow-hidden rounded-lg bg-slate-100 dark:bg-white/5">
                                        @if ($post->cover_url)<img src="{{ $post->cover_url }}" alt="" class="size-full object-cover">@else<div class="grid size-full place-items-center text-slate-400"><x-icon name="file-text" class="size-5" /></div>@endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ $post->title }}</p>
                                        <p class="truncate text-xs text-slate-500">/blog/{{ $post->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell">
                                @if ($post->is_published && $post->published_at?->isFuture())
                                    <span class="rounded-full bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-600">Scheduled</span>
                                @elseif ($post->is_published)
                                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600">Published</span>
                                @else
                                    <span class="rounded-full bg-slate-500/10 px-2.5 py-1 text-xs font-semibold text-slate-500">Draft</span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell">{{ number_format($post->views) }}</td>
                            <td class="hidden text-slate-500 lg:table-cell">{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('blog.show', $post) }}" target="_blank" class="text-slate-400 hover:text-accent" title="View"><x-icon name="eye" class="size-4" /></a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-slate-400 hover:text-accent" title="Edit"><x-icon name="edit" class="size-4" /></a>
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" data-confirm="Delete this post?">
                                        @csrf @method('DELETE')
                                        <button class="cursor-pointer text-slate-400 hover:text-rose-500" title="Delete"><x-icon name="trash" class="size-4" /></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</x-layouts.admin>
