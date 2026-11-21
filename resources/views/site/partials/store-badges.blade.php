{{-- App Store / Google Play badges. $size: 'sm' (cards) or 'lg' (project page) --}}
@php $lg = ($size ?? 'sm') === 'lg'; @endphp
@if ($project->app_store_url || $project->play_store_url)
    <div class="flex flex-wrap gap-2">
        @foreach ([
            ['url' => $project->app_store_url, 'icon' => 'apple', 'top' => 'Download on the', 'name' => 'App Store'],
            ['url' => $project->play_store_url, 'icon' => 'google-play', 'top' => 'Get it on', 'name' => 'Google Play'],
        ] as $store)
            @if ($store['url'])
                <a href="{{ $store['url'] }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-black dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 {{ $lg ? 'px-4 py-2.5' : 'px-3 py-1.5' }}">
                    <x-icon :name="$store['icon']" class="{{ $lg ? 'size-7' : 'size-5' }}" />
                    <span class="text-left leading-tight">
                        <span class="block {{ $lg ? 'text-[10px]' : 'text-[9px]' }} uppercase tracking-wide opacity-75">{{ $store['top'] }}</span>
                        <span class="block font-semibold {{ $lg ? 'text-base' : 'text-xs' }}">{{ $store['name'] }}</span>
                    </span>
                </a>
            @endif
        @endforeach
    </div>
@endif
