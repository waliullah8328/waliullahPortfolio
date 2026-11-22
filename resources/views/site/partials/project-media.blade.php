{{--
    Project preview shown inside device mockups.
    website -> browser window, app -> phone, both -> browser + phone.
    Without an uploaded image a styled UI skeleton (with the project title) is drawn instead.
    $large = true for the bigger spotlight / detail versions.
--}}
@php
    $large = $large ?? false;
    $img = $project->image_url;
    $screen = $project->screen_image_url;
    // Phones are small: ask Drive for a phone-sized copy so text in screenshots stays crisp.
    $phone = fn (?string $path) => \App\Support\Media::url($path, false, $large ? 900 : 600);
    $screenPhone = $phone($project->screen_image);
    $domain = $project->live_url ? parse_url($project->live_url, PHP_URL_HOST) : str($project->title)->slug().'.app';
    $showBrowser = $project->type !== 'app';
    $showPhone = $project->is_app;
@endphp
<div class="pm-stage {{ $large ? 'pm-large' : '' }}">
    <div class="pm-grid"></div>

    @if ($showBrowser)
        <div class="pm-browser {{ $showPhone ? 'pm-browser-shift' : '' }}">
            <div class="flex items-center gap-2 border-b border-slate-200/80 bg-slate-50 px-3 py-2 dark:border-white/5 dark:bg-[#12121e]">
                <span class="flex gap-1"><i class="pm-dot"></i><i class="pm-dot"></i><i class="pm-dot"></i></span>
                <span class="mx-auto flex max-w-[65%] items-center gap-1 truncate rounded-md bg-white px-2.5 py-0.5 text-[9px] text-slate-400 ring-1 ring-slate-200 dark:bg-white/5 dark:ring-white/5 {{ $large ? 'sm:text-[11px]' : '' }}">
                    <x-icon name="globe" class="size-2.5 shrink-0" /> <span class="truncate">{{ $domain }}</span>
                </span>
            </div>
            @if ($img)
                <img src="{{ $img }}" alt="{{ $project->title }}" loading="lazy" class="pm-shot">
            @else
                <div class="pm-shot flex flex-col bg-white p-3 dark:bg-[#0e0e19] {{ $large ? 'sm:p-6' : '' }}">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="pm-logo"></i><i class="pm-bar w-10"></i></span>
                        <span class="flex gap-1.5"><i class="pm-bar w-5"></i><i class="pm-bar w-5"></i><i class="pm-bar w-5"></i></span>
                    </div>
                    <div class="mt-[8%] max-w-[75%]">
                        <p class="line-clamp-2 font-display font-bold leading-tight text-slate-900 dark:text-white {{ $large ? 'text-lg sm:text-3xl' : 'text-sm sm:text-base' }}">{{ $project->title }}</p>
                        <p class="mt-1.5 line-clamp-1 text-slate-400 {{ $large ? 'text-xs sm:text-sm' : 'text-[9px]' }}">{{ $project->summary }}</p>
                        <span class="mt-3 inline-flex gap-1.5"><i class="pm-btn"></i><i class="pm-btn pm-btn-ghost"></i></span>
                    </div>
                    <div class="mt-auto grid grid-cols-3 gap-2 pt-3">
                        <i class="pm-card"></i><i class="pm-card"></i><i class="pm-card"></i>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($showPhone)
        @unless ($showBrowser)
            {{-- second phone behind for depth --}}
            <div class="pm-phone pm-phone-back {{ $screen ? 'has-img' : '' }}" aria-hidden="true">
                <div class="pm-screen bg-slate-100 dark:bg-[#141422]">
                    @if ($screenPhone)<img src="{{ $screenPhone }}" alt="" loading="lazy" class="size-full object-cover object-top">@endif
                </div>
            </div>
        @endunless
        @php $phoneImg = $showBrowser && $screenPhone ? $screenPhone : $phone($project->image); @endphp
        <div class="pm-phone {{ $showBrowser ? 'pm-phone-side' : 'pm-phone-center' }}">
            <span class="pm-notch"></span>
            <div class="pm-screen">
                @if ($phoneImg)
                    <img src="{{ $phoneImg }}" alt="" loading="lazy" class="size-full object-cover object-top">
                @else
                    <div class="flex size-full flex-col bg-white px-2.5 pt-5 pb-2 dark:bg-[#0e0e19]">
                        <div class="flex items-center justify-between"><i class="pm-bar w-6"></i><i class="pm-logo size-3!"></i></div>
                        <p class="mt-3 line-clamp-2 font-display text-[10px] font-bold leading-tight text-slate-900 dark:text-white">{{ $project->title }}</p>
                        <div class="pm-hero mt-2"></div>
                        <div class="mt-2 space-y-1.5">
                            @foreach ([1, 2, 3] as $n)
                                <div class="flex items-center gap-1.5"><i class="pm-logo size-3! opacity-60"></i><i class="pm-bar flex-1"></i></div>
                            @endforeach
                        </div>
                        <div class="mt-auto flex justify-around border-t border-slate-100 pt-1.5 dark:border-white/5">
                            <i class="pm-tab is-on"></i><i class="pm-tab"></i><i class="pm-tab"></i><i class="pm-tab"></i>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
