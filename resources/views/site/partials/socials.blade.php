@php
    $links = collect(['github' => 'GitHub', 'linkedin' => 'LinkedIn', 'twitter' => 'X', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'dribbble' => 'Dribbble', 'youtube' => 'YouTube'])
        ->filter(fn ($label, $key) => setting($key));
@endphp
@if ($links->isNotEmpty())
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($links as $key => $label)
            <a href="{{ setting($key) }}" target="_blank" rel="noopener" aria-label="{{ $label }}"
               class="grid {{ $size ?? 'size-10' }} place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:-translate-y-0.5 hover:border-accent hover:text-accent dark:border-white/10 dark:text-slate-400 dark:hover:text-white">
                <x-icon :name="$key" class="size-4" />
            </a>
        @endforeach
    </div>
@endif
