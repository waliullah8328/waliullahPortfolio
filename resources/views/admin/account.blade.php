<x-layouts.admin title="Account">
    <form method="POST" action="{{ route('admin.account') }}" class="card max-w-2xl space-y-5 p-6 sm:p-8">
        @csrf @method('PUT')
        <div>
            <h2 class="text-lg font-semibold">Login details</h2>
            <p class="text-sm text-slate-500">Change the email and password you use to sign in to this dashboard.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="label" for="name">Name</label><input id="name" name="name" value="{{ old('name', $user->name) }}" class="input" required></div>
            <div><label class="label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required></div>
        </div>
        <hr class="border-slate-200 dark:border-white/10">
        <div class="grid gap-5 sm:grid-cols-3">
            <div><label class="label" for="current_password">Current password</label><input id="current_password" type="password" name="current_password" class="input" autocomplete="current-password"></div>
            <div><label class="label" for="password">New password</label><input id="password" type="password" name="password" class="input" autocomplete="new-password"></div>
            <div><label class="label" for="password_confirmation">Confirm</label><input id="password_confirmation" type="password" name="password_confirmation" class="input" autocomplete="new-password"></div>
        </div>
        <p class="help">Leave the password fields empty to keep your current password.</p>
        <button class="btn btn-primary">Save changes</button>
    </form>
</x-layouts.admin>
