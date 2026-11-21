@props(['name', 'label', 'checked' => false, 'help' => null])
<label class="flex cursor-pointer items-start justify-between gap-4 rounded-xl border border-slate-200 p-4 dark:border-white/10">
    <span>
        <span class="block text-sm font-medium text-slate-800 dark:text-slate-200">{{ $label }}</span>
        @if ($help)<span class="help block">{{ $help }}</span>@endif
    </span>
    <span class="switch">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)>
        <span></span>
    </span>
</label>
