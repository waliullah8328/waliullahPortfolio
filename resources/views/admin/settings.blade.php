<x-layouts.admin title="Settings">
    <div class="grid items-start gap-6 lg:grid-cols-[240px_1fr]">
        <nav class="glass flex gap-1 overflow-x-auto rounded-3xl p-2 lg:sticky lg:top-28 lg:flex-col">
            @foreach ($groups as $key => $group)
                <a href="{{ route('admin.settings', ['tab' => $key]) }}" class="side-link shrink-0 {{ $tab === $key ? 'active' : '' }}">
                    <x-icon :name="$group['icon']" class="size-[18px]" /> {{ $group['label'] }}
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('admin.settings') }}" enctype="multipart/form-data" class="card p-6 sm:p-8">
            @csrf
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="mb-8 flex items-center gap-4">
                <span class="stat-icon grid size-12 shrink-0 place-items-center rounded-2xl text-accent" style="background: color-mix(in oklab, var(--accent) 12%, transparent)"><x-icon :name="$groups[$tab]['icon']" class="size-5" /></span>
                <div>
                    <h2 class="text-xl font-bold tracking-tight">{{ $groups[$tab]['label'] }}</h2>
                    <p class="text-sm text-slate-500">Changes appear on your website immediately after saving.</p>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                @foreach ($groups[$tab]['fields'] as $key => $field)
                    @php $value = old($key, setting($key)); @endphp
                    <div class="{{ in_array($field['type'], ['textarea', 'image', 'file', 'toggle']) ? 'md:col-span-2' : '' }}">
                        @switch($field['type'])
                            @case('image')
                                <x-admin.image-input :name="$key" :label="$field['label']" :current="media(setting($key))" :link="setting($key)" :remove-name="'remove_'.$key" />
                                @break
                            @case('file')
                                <x-admin.image-input :name="$key" :label="$field['label']" :current="media(setting($key), true)" :link="setting($key)" :file="true" :accept="$field['accept'] ?? '*'" help="PDF or Word document, up to 10 MB. Visitors open it from the Resume button." :remove-name="'remove_'.$key" />
                                @break
                            @case('toggle')
                                <x-admin.toggle :name="$key" :label="$field['label']" :checked="setting_on($key)" />
                                @break
                            @case('textarea')
                                <label class="label" for="{{ $key }}">{{ $field['label'] }}</label>
                                <textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $field['rows'] ?? 4 }}" class="input">{{ $value }}</textarea>
                                @break
                            @case('select')
                                <label class="label" for="{{ $key }}">{{ $field['label'] }}</label>
                                <select id="{{ $key }}" name="{{ $key }}" class="input">
                                    @foreach ($field['options'] as $opt => $optLabel)
                                        <option value="{{ $opt }}" @selected($value === $opt)>{{ $optLabel }}</option>
                                    @endforeach
                                </select>
                                @break
                            @case('color')
                                <label class="label" for="{{ $key }}">{{ $field['label'] }}</label>
                                <div class="flex items-center gap-3">
                                    <input id="{{ $key }}" type="color" name="{{ $key }}" value="{{ $value ?: '#7c3aed' }}" class="h-11 w-16 cursor-pointer rounded-lg border border-slate-300 bg-transparent p-1 dark:border-white/10"
                                           oninput="this.nextElementSibling.textContent = this.value">
                                    <code class="text-sm text-slate-500">{{ $value ?: '#7c3aed' }}</code>
                                </div>
                                @break
                            @default
                                <label class="label" for="{{ $key }}">{{ $field['label'] }}</label>
                                <input id="{{ $key }}" type="{{ $field['type'] }}" name="{{ $key }}" value="{{ $value }}" class="input">
                        @endswitch
                        @if (! empty($field['help']))<p class="help">{{ $field['help'] }}</p>@endif
                    </div>
                @endforeach
            </div>

            <div class="sticky bottom-3 -mx-3 mt-8 flex items-center gap-3 rounded-2xl border border-slate-200/70 bg-white/80 p-3 backdrop-blur-xl sm:-mx-5 dark:border-white/[0.08] dark:bg-[#0e0e18]/80">
                <button class="btn btn-primary"><x-icon name="check" class="size-4" /> Save settings</button>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost">Preview site</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
