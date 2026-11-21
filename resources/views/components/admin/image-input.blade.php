@props(['name', 'label' => 'Image', 'current' => null, 'link' => null, 'help' => 'PNG, JPG, WEBP or GIF, up to 5 MB.', 'accept' => 'image/*', 'file' => false, 'removeName' => 'remove_image'])
@php
    $id = 'pv-'.$name;
    // Show the saved link (not the converted one) so it can be edited.
    $savedLink = \App\Support\Media::isExternal($link) ? $link : null;
@endphp
<div>
    <span class="label">{{ $label }}</span>
    <div class="flex flex-col gap-4 rounded-2xl border border-dashed border-slate-300 p-4 sm:flex-row sm:items-start dark:border-white/15">
        @unless ($file)
            <div class="relative grid size-24 shrink-0 place-items-center overflow-hidden rounded-xl bg-slate-100 dark:bg-white/5">
                <img id="{{ $id }}" src="{{ $current }}" alt="" referrerpolicy="no-referrer" class="size-full object-cover {{ $current ? '' : 'hidden' }}">
                <x-icon name="image" data-placeholder class="size-8 text-slate-400 {{ $current ? 'hidden' : '' }}" />
            </div>
        @endunless
        <div class="min-w-0 flex-1 space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                <label class="btn btn-ghost btn-sm">
                    <x-icon name="upload" class="size-4" /> {{ $current ? 'Replace' : 'Upload' }}
                    <input type="file" name="{{ $name }}" accept="{{ $accept }}" class="sr-only" data-preview="{{ $id }}">
                </label>
                <span class="text-xs font-medium text-slate-400">or paste a link</span>
            </div>
            <p id="{{ $id }}-name" class="truncate text-xs text-slate-500">
                @if ($file && $current)<a href="{{ $current }}" target="_blank" rel="noopener" class="text-accent underline">Current file</a>@endif
            </p>
            <div class="relative">
                <x-icon name="globe" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input type="url" name="{{ $name }}_link" value="{{ old($name.'_link', $savedLink) }}" inputmode="url"
                       placeholder="https://drive.google.com/file/d/…/view" class="input pl-10 text-sm" aria-label="{{ $label }} link">
            </div>
            <p class="help">
                {{ $help }} Using Google Drive? Set sharing to <strong>“Anyone with the link”</strong>, then paste the share link here.
            </p>
            @if ($current)
                <label class="inline-flex items-center gap-2 text-xs text-rose-500">
                    <input type="checkbox" name="{{ $removeName }}" value="1" class="rounded"> Remove current {{ $file ? 'file' : 'image' }}
                </label>
            @endif
        </div>
    </div>
</div>
