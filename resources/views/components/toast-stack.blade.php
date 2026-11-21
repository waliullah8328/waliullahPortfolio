@php
    // Toasts for results that arrive with a normal page load (form posted without JavaScript,
    // or a redirect). Background (fetch) submits create the same toasts in resources/js/app.js.
    $toasts = collect();
    foreach (['contact_success' => 'Message sent', 'recommend_success' => 'Thank you!', 'toast_success' => 'Success'] as $key => $title) {
        if (session($key)) {
            $toasts->push(['success', $title, session($key)]);
        }
    }
    if (session('toast_error')) {
        $toasts->push(['error', 'Not sent', session('toast_error')]);
    }
    foreach (['default', 'recommend'] as $bag) {
        if ($errors->getBag($bag)->any()) {
            $toasts->push(['error', 'Please fix the form', $errors->getBag($bag)->first()]);
        }
    }
@endphp
<div id="toast-stack" class="toast-stack" aria-live="polite">
    @foreach ($toasts as [$type, $title, $message])
        <div class="toast toast-{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}" data-toast-ms="{{ $type === 'error' ? 7000 : 5000 }}">
            <span class="toast-icon"><x-icon :name="$type === 'error' ? 'x' : 'check'" class="size-4" /></span>
            <div class="min-w-0">
                <p class="toast-title">{{ $title }}</p>
                <p class="mt-0.5 text-slate-600 dark:text-slate-300">{{ $message }}</p>
            </div>
            <button type="button" class="toast-close" aria-label="Close">&times;</button>
        </div>
    @endforeach
</div>
