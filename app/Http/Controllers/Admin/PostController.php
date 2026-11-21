<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post(['is_published' => true, 'published_at' => now()])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Post::class, $request->slug, $data['title']);
        $data['cover_image'] = Media::replace($request->file('cover_image'), null, 'blog', $request->input('cover_image_link'));

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Post::class, $request->slug, $data['title'], $post);

        if ($request->boolean('remove_image')) {
            Media::delete($post->cover_image);
            $post->cover_image = null;
        }
        $data['cover_image'] = Media::replace($request->file('cover_image'), $post->cover_image, 'blog', $request->input('cover_image_link'));

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        Media::delete($post->cover_image);
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    /** Live markdown preview for the editor. */
    public function preview(Request $request)
    {
        return response()->json([
            'html' => Str::markdown((string) $request->input('body'), ['html_input' => 'strip', 'allow_unsafe_links' => false]),
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:210'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'cover_image_link' => ['nullable', 'url', 'max:2048'],
        ]);
        unset($data['cover_image'], $data['cover_image_link'], $data['slug']);

        $published = $request->boolean('is_published');

        return array_merge($data, [
            'is_published' => $published,
            'published_at' => $data['published_at'] ?? ($published ? now() : null),
        ]);
    }
}
