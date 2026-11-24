<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function home()
    {
        // The top featured project gets a large spotlight; the grid shows the next ones.
        $spotlight = Project::published()->where('is_featured', true)->ordered()->first();

        return view('site.home', [
            'services' => Service::where('is_visible', true)->ordered()->get(),
            'skillGroups' => $this->orderSkillGroups(Skill::where('is_visible', true)->ordered()->get()->groupBy('category')),
            'experiences' => Experience::ordered()->get()->groupBy('type'),
            'certificates' => Certificate::visible()->ordered()->get(),
            'spotlight' => $spotlight,
            'projects' => Project::published()->when($spotlight, fn ($q) => $q->whereKeyNot($spotlight->id))
                ->ordered()->take((int) setting('home_projects_count', 6))->get(),
            'projectCategories' => $this->usedCategories(),
            'testimonials' => Testimonial::where('is_visible', true)->ordered()->get(),
            'posts' => Post::published()->take(3)->get(),
        ]);
    }

    /**
     * Skill tabs in a fixed order: Mobile, State & Architecture, Tools & Dev, then Web & Backend.
     * Matched by keyword so a renamed category still lands in place; any others follow in their own order.
     */
    private function orderSkillGroups($groups)
    {
        $rank = function (string $category): int {
            $c = mb_strtolower($category);

            return match (true) {
                str_contains($c, 'mobile') => 0,
                str_contains($c, 'state') || str_contains($c, 'architecture') => 1,
                str_contains($c, 'tool') || str_contains($c, 'dev') => 2,
                str_contains($c, 'web') || str_contains($c, 'backend') => 3,
                default => 4,
            };
        };
        $position = $groups->keys()->flip();

        return $groups->sortBy(fn ($_, $cat) => [$rank((string) $cat), $position[$cat]]);
    }

    public function projects(Request $request)
    {
        $projects = Project::published()
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->when($request->type === 'app', fn ($q) => $q->whereIn('type', ['app', 'both']))
            ->when($request->type === 'website', fn ($q) => $q->whereIn('type', ['website', 'both']))
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('site.projects', [
            'projects' => $projects,
            'categories' => $this->usedCategories(),
        ]);
    }

    public function project(Project $project)
    {
        abort_unless($project->is_published || Auth::check(), 404);

        return view('site.project', [
            'project' => $project,
            'more' => Project::published()->whereKeyNot($project->id)->ordered()->take(3)->get(),
        ]);
    }

    public function blog(Request $request)
    {
        $posts = Post::published()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('body', 'like', "%{$s}%")))
            ->when($request->tag, fn ($q, $t) => $q->where('tags', 'like', "%{$t}%"))
            ->paginate(9)
            ->withQueryString();

        return view('site.blog', compact('posts'));
    }

    public function post(Post $post)
    {
        $visible = $post->is_published && (! $post->published_at || $post->published_at->isPast());
        abort_unless($visible || Auth::check(), 404);

        if (! Auth::check()) {
            $post->increment('views');
        }

        return view('site.post', [
            'post' => $post,
            'related' => Post::published()->whereKeyNot($post->id)->take(3)->get(),
        ]);
    }

    public function contact(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'], // honeypot field hidden from humans
        ]);

        try {
            Message::create($data + ['ip_address' => $request->ip()]);
        } catch (\Throwable $e) {
            report($e);

            return $this->failed($request, 'contact', "Sorry, your message couldn't be sent right now. Please try again in a minute or email me directly.");
        }

        return $this->sent($request, 'contact', 'contact_success', 'Thanks! Your message has been sent. I will get back to you soon.');
    }

    /** Success: JSON for background (fetch) submits, otherwise redirect back with a toast. */
    private function sent(Request $request, string $anchor, string $flashKey, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->to(url()->previous().'#'.$anchor)->with($flashKey, $message);
    }

    private function failed(Request $request, string $anchor, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message], 500)
            : redirect()->to(url()->previous().'#'.$anchor)->withInput()->with('toast_error', $message);
    }

    public function recommend(Request $request)
    {
        $data = $request->validateWithBag('recommend', [
            'name' => ['required', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'min:20', 'max:1500'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'website' => ['prohibited'],
        ]);

        // Visitor recommendations stay hidden until approved in the admin panel.
        try {
            Testimonial::create($data + ['is_visible' => false, 'sort_order' => 999]);
        } catch (\Throwable $e) {
            report($e);

            return $this->failed($request, 'testimonials', "Sorry, your recommendation couldn't be saved right now. Please try again in a minute.");
        }

        return $this->sent($request, 'testimonials', 'recommend_success', 'Thank you for the recommendation! It will appear after review.');
    }

    public function cv()
    {
        $path = Setting::get('cv');
        if (\App\Support\Media::isExternal($path)) {
            return redirect()->away(\App\Support\Media::url($path, true));
        }
        abort_unless($path && is_file(public_path($path)), 404, 'CV not uploaded yet.');

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $name = str(setting('name', 'cv'))->slug().'-cv.'.$ext;

        return response()->download(public_path($path), $name);
    }

    /** Categories that have at least one published project, in the admin's order. */
    private function usedCategories()
    {
        $used = Project::published()->whereNotNull('category')->distinct()->pluck('category');
        $ordered = ProjectCategory::ordered()->pluck('name');

        // Anything not in the managed list (older data) goes last instead of disappearing.
        return $ordered->intersect($used)->values()->merge($used->diff($ordered)->sort()->values());
    }
}
