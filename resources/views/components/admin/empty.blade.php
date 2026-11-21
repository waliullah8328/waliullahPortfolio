@props(['icon' => 'inbox', 'title' => 'Nothing here yet', 'action' => null, 'actionLabel' => 'Add new'])
<div class="card flex flex-col items-center px-6 py-16 text-center">
    <span class="grid size-14 place-items-center rounded-2xl bg-accent/10 text-accent"><x-icon :name="$icon" class="size-7" /></span>
    <p class="mt-4 font-semibold text-slate-900 dark:text-white">{{ $title }}</p>
    <p class="mt-1 text-sm text-slate-500">{{ $slot }}</p>
    @if ($action)
        <a href="{{ $action }}" class="btn btn-primary mt-6"><x-icon name="plus" class="size-4" /> {{ $actionLabel }}</a>
    @endif
</div>
