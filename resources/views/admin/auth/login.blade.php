<!DOCTYPE html>
<html lang="en" class="dark" style="--accent: {{ setting('accent_color', '#7c3aed') }}; --accent-2: {{ setting('accent_color_2', '#06b6d4') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Admin login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center overflow-hidden px-4 font-sans">
    <div class="bg-grid absolute inset-0 -z-10"></div>
    <div class="blob -z-10 -top-20 -left-20 size-96" style="background: var(--accent)"></div>
    <div class="blob -z-10 -bottom-20 -right-20 size-96" style="background: var(--accent-2)"></div>

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <span class="mx-auto grid size-14 place-items-center rounded-2xl text-white shadow-xl shadow-accent/30" style="background-image: linear-gradient(135deg, var(--accent), var(--accent-2))"><x-icon name="sparkles" class="size-7" /></span>
            <h1 class="mt-5 text-2xl font-bold">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500">Sign in to manage your portfolio</p>
        </div>
        <form method="POST" action="{{ route('admin.login') }}" class="card space-y-5 p-8 backdrop-blur-xl">
            @csrf
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="input" required autofocus autocomplete="username">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="password">Password</label>
                <input id="password" type="password" name="password" class="input" required autocomplete="current-password">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                <input type="checkbox" name="remember" value="1" class="rounded"> Remember me
            </label>
            <button class="btn btn-primary w-full">Sign in <x-icon name="arrow-right" class="size-4" /></button>
        </form>
        <p class="mt-6 text-center text-sm"><a href="{{ route('home') }}" class="text-slate-500 hover:text-accent">← Back to website</a></p>
    </div>
</body>
</html>
