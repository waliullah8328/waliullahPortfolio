<x-layouts.admin title="Certificates">
    <x-slot:actions>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> <span class="hidden sm:inline">Add certificate</span></a>
    </x-slot:actions>

    <p class="mb-6 text-sm text-slate-500">These appear in the <strong>Certifications</strong> section of your home page.</p>

    @if ($certificates->isEmpty())
        <x-admin.empty icon="badge-check" title="No certificates yet" :action="route('admin.certificates.create')" action-label="Add your first certificate">Add courses, certifications and awards with a photo of the certificate and a link to verify it.</x-admin.empty>
    @else
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($certificates as $cert)
                <div class="card flex flex-col overflow-hidden {{ $cert->is_visible ? '' : 'opacity-60' }}">
                    <div class="relative aspect-[16/10] bg-slate-100 dark:bg-white/5">
                        @if ($cert->image_url)
                            <img src="{{ $cert->image_url }}" alt="" referrerpolicy="no-referrer" class="size-full object-cover">
                        @else
                            <div class="grid size-full place-items-center text-slate-400"><x-icon name="badge-check" class="size-10" /></div>
                        @endif
                        @unless ($cert->is_visible)
                            <span class="absolute left-3 top-3 rounded-full bg-black/60 px-2.5 py-0.5 text-xs font-semibold text-white">Hidden</span>
                        @endunless
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $cert->title }}</p>
                        <p class="text-sm text-slate-500">{{ $cert->issuer }}@if ($cert->issued_at) · {{ $cert->issued_at->format('M Y') }}@endif</p>
                        @if ($cert->credential_url)
                            <a href="{{ $cert->credential_url }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-accent hover:underline"><x-icon name="external" class="size-3.5" /> Credential link</a>
                        @endif
                        <div class="min-h-4 flex-1"></div>
                        <div class="flex items-center gap-2 border-t border-slate-100 pt-4 dark:border-white/5">
                            <a href="{{ route('admin.certificates.edit', $cert) }}" class="btn btn-ghost btn-sm flex-1"><x-icon name="edit" class="size-4" /> Edit</a>
                            <form method="POST" action="{{ route('admin.certificates.destroy', $cert) }}" data-confirm="Delete {{ $cert->title }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-rose-500!" title="Delete"><x-icon name="trash" class="size-4" /></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.admin>
