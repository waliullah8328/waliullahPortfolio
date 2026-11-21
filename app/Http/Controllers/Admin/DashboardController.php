<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Testimonial;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Projects', 'value' => Project::count(), 'icon' => 'folder', 'route' => 'admin.projects.index'],
                ['label' => 'Skills', 'value' => Skill::count(), 'icon' => 'layers', 'route' => 'admin.skills.index'],
                ['label' => 'Experience', 'value' => Experience::count(), 'icon' => 'briefcase', 'route' => 'admin.experiences.index'],
                ['label' => 'Blog posts', 'value' => Post::count(), 'icon' => 'file-text', 'route' => 'admin.posts.index'],
                ['label' => 'Recommendations', 'value' => Testimonial::count(), 'icon' => 'award', 'route' => 'admin.testimonials.index'],
                ['label' => 'Unread messages', 'value' => Message::where('is_read', false)->count(), 'icon' => 'mail', 'route' => 'admin.messages.index'],
            ],
            'blogViews' => (int) Post::sum('views'),
            'pendingTestimonials' => Testimonial::where('is_visible', false)->count(),
            'recentMessages' => Message::latest()->take(5)->get(),
            'recentPosts' => Post::latest()->take(5)->get(),
            'messagesChart' => collect(range(6, 0))->map(fn ($d) => [
                'day' => now()->subDays($d)->format('D'),
                'count' => Message::whereDate('created_at', now()->subDays($d)->toDateString())->count(),
            ]),
        ]);
    }
}
