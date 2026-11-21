<x-layouts.admin title="Message">
    <a href="{{ route('admin.messages.index') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-accent"><x-icon name="arrow-left" class="size-4" /> Back to inbox</a>

    <div class="card max-w-3xl p-6 sm:p-8">
        <div class="flex flex-wrap items-start gap-4">
            <span class="grid size-12 shrink-0 place-items-center rounded-full bg-accent/10 text-lg font-semibold text-accent">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl font-semibold">{{ $message->subject ?: 'No subject' }}</h2>
                <p class="text-sm text-slate-500"><span class="font-medium text-slate-800 dark:text-slate-200">{{ $message->name }}</span> &lt;{{ $message->email }}&gt;</p>
                <p class="mt-1 text-xs text-slate-400">{{ $message->created_at->format('M d, Y \a\t H:i') }} · {{ $message->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <div class="mt-8 whitespace-pre-line rounded-xl bg-slate-50 p-5 leading-relaxed text-slate-700 dark:bg-white/[0.03] dark:text-slate-300">{{ $message->message }}</div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Your message')) }}" class="btn btn-primary"><x-icon name="reply" class="size-4" /> Reply by email</a>
            <form method="POST" action="{{ route('admin.messages.toggle', $message) }}">
                @csrf @method('PATCH')
                <button class="btn btn-ghost"><x-icon name="mail" class="size-4" /> Mark as unread</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message?">
                @csrf @method('DELETE')
                <button class="btn btn-danger"><x-icon name="trash" class="size-4" /> Delete</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
