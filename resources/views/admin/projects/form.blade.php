@php $editing = $project->exists; @endphp
<x-layouts.admin :title="$editing ? 'Edit project' : 'New project'">
    <form method="POST" action="{{ $editing ? route('admin.projects.update', $project) : route('admin.projects.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[1fr_340px]">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card space-y-5 p-6">
            <div>
                <label class="label" for="title">Title *</label>
                <input id="title" name="title" value="{{ old('title', $project->title) }}" class="input" required data-slug-source>
            </div>
            <div>
                <span class="label">Project type</span>
                <div class="grid gap-3 sm:grid-cols-3" data-type-picker>
                    @foreach (['website' => ['Website', 'globe'], 'app' => ['Mobile App', 'smartphone'], 'both' => ['Web + App', 'layers']] as $val => [$lbl, $icon])
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 transition has-[:checked]:border-accent has-[:checked]:bg-accent/5 dark:border-white/10">
                            <input type="radio" name="type" value="{{ $val }}" @checked(old('type', $project->type ?? 'website') === $val) class="sr-only">
                            <x-icon :name="$icon" class="size-5 text-accent" /><span class="text-sm font-medium">{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="slug">URL slug</label>
                    <input id="slug" name="slug" value="{{ old('slug', $project->slug) }}" class="input" data-slug-target>
                    <p class="help">Leave empty to generate from the title.</p>
                </div>
                <div>
                    @php
                        $categoryNames = \App\Models\ProjectCategory::ordered()->pluck('name');
                        $currentCategory = old('category', $project->category);
                    @endphp
                    <label class="label" for="category">Category</label>
                    <select id="category" name="category" class="input">
                        <option value="">No category</option>
                        @foreach ($categoryNames as $c)
                            <option value="{{ $c }}" @selected($currentCategory === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="error">{{ $message }}</p>@enderror
                    <p class="help"><a href="{{ route('admin.categories.index') }}" class="font-medium text-accent hover:underline">Manage categories</a> to add or rename them.</p>
                </div>
            </div>
            <div>
                <label class="label" for="summary">Short summary</label>
                <textarea id="summary" name="summary" rows="2" class="input" maxlength="500">{{ old('summary', $project->summary) }}</textarea>
                <p class="help">Shown on project cards.</p>
            </div>
            <div>
                <label class="label" for="description">Full description (Markdown)</label>
                <textarea id="description" name="description" rows="12" class="input font-mono text-[13px]">{{ old('description', $project->description) }}</textarea>
                <p class="help">Use ## for headings, - for bullet lists, **bold**, [links](https://...).</p>
            </div>
            <div>
                <label class="label" for="tech_stack">Tech stack</label>
                <input id="tech_stack" name="tech_stack" value="{{ old('tech_stack', $project->tech_stack) }}" class="input" placeholder="Laravel, Vue, Tailwind, MySQL">
                <p class="help">Comma separated.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="label" for="live_url">Live URL</label><input id="live_url" type="url" name="live_url" value="{{ old('live_url', $project->live_url) }}" class="input" placeholder="https://"></div>
                <div><label class="label" for="repo_url">Repository URL</label><input id="repo_url" type="url" name="repo_url" value="{{ old('repo_url', $project->repo_url) }}" class="input" placeholder="https://github.com/..."></div>
            </div>
            <div data-app-fields class="space-y-3 rounded-xl border border-accent/30 bg-accent/5 p-4">
                <p class="flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white"><x-icon name="smartphone" class="size-4 text-accent" /> Mobile app store links</p>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="label flex items-center gap-1.5" for="app_store_url"><x-icon name="apple" class="size-4" /> Apple App Store URL</label>
                        <input id="app_store_url" type="url" name="app_store_url" value="{{ old('app_store_url', $project->app_store_url) }}" class="input" placeholder="https://apps.apple.com/app/...">
                    </div>
                    <div>
                        <label class="label flex items-center gap-1.5" for="play_store_url"><x-icon name="google-play" class="size-4" /> Google Play Store URL</label>
                        <input id="play_store_url" type="url" name="play_store_url" value="{{ old('play_store_url', $project->play_store_url) }}" class="input" placeholder="https://play.google.com/store/apps/details?id=...">
                    </div>
                </div>
                <p class="help">Leave either empty if the app isn't on that store. Filled links appear as official-style download badges.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card space-y-4 p-6">
                <x-admin.image-input name="image" label="Cover image" :current="$project->image_url" :link="$project->image" />
                <x-admin.image-input name="screen_image" label="Second screen image" remove-name="remove_screen_image"
                    :current="$project->screen_image_url" :link="$project->screen_image"
                    help="Mobile App: shown on the phone behind. Web + App: shown on the phone next to the browser. Portrait screenshot works best." />
            </div>
            <div class="card space-y-3 p-6">
                <x-admin.toggle name="is_published" label="Published" help="Visible on the website" :checked="old('is_published', $project->is_published)" />
                <x-admin.toggle name="is_featured" label="Featured" help="Shown first with a badge" :checked="old('is_featured', $project->is_featured)" />
                <div>
                    <label class="label" for="sort_order">Sort order</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}" class="input">
                    <p class="help">Lower numbers appear first.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="btn btn-primary flex-1"><x-icon name="check" class="size-4" /> {{ $editing ? 'Save changes' : 'Create project' }}</button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
