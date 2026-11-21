<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin login comes from ADMIN_EMAIL / ADMIN_PASSWORD. The repo is public, so the
        // demo password is only allowed locally, never on a live server.
        $password = env('ADMIN_PASSWORD');
        if (! $password) {
            if (app()->isProduction()) {
                throw new \RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD before seeding a live database.');
            }
            $password = 'password';
        }
        User::updateOrCreate(['email' => env('ADMIN_EMAIL', 'admin@example.com')], [
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => Hash::make($password),
        ]);

        Setting::put([
            'name' => 'Alex Morgan',
            'role' => 'Full-Stack Developer',
            'typed_words' => 'Laravel Expert, UI Enthusiast, Problem Solver, Open-Source Contributor',
            'hero_intro' => 'I design and build fast, accessible and beautiful web applications with PHP, Laravel and modern JavaScript, from idea to launch.',
            'available_for_work' => '1',
            'about_title' => 'Turning ideas into delightful digital products',
            'about' => "I'm a developer who loves crafting clean, maintainable code and intuitive interfaces. Over the years I've helped startups and agencies ship **web apps, APIs and dashboards** that scale.\n\nWhen I'm not coding, you'll find me writing about what I learn, contributing to open source, or exploring new design trends.",
            'years_experience' => '5+',
            'projects_completed' => '40+',
            'happy_clients' => '25+',
            'email' => 'hello@example.com',
            'phone' => '+1 555 010 2030',
            'location' => 'Remote · Worldwide',
            'github' => 'https://github.com',
            'linkedin' => 'https://linkedin.com',
            'twitter' => 'https://x.com',
            'accent_color' => '#7c3aed',
            'accent_color_2' => '#06b6d4',
            'default_theme' => 'dark',
            'home_projects_count' => '6',
            'show_about' => '1', 'show_skills' => '1', 'show_experience' => '1', 'show_projects' => '1',
            'show_testimonials' => '1', 'allow_recommendations' => '1', 'show_blog' => '1', 'show_contact' => '1',
            'site_title' => 'Alex Morgan · Full-Stack Developer',
            'meta_description' => 'Portfolio of Alex Morgan, a full-stack Laravel developer building modern web applications.',
        ]);

        if (Skill::exists()) {
            return; // demo content already seeded
        }

        $icon = fn ($n, $v = 'original') => "https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/icons/{$n}/{$n}-{$v}.svg";
        $skills = [
            'Backend' => [['PHP', 95, $icon('php')], ['Laravel', 95, $icon('laravel')], ['MySQL', 85, $icon('mysql')], ['REST APIs', 90, null], ['Redis', 70, $icon('redis')]],
            'Frontend' => [['JavaScript', 90, $icon('javascript')], ['Vue.js', 85, $icon('vuejs')], ['React', 75, $icon('react')], ['Tailwind CSS', 95, $icon('tailwindcss')], ['HTML & CSS', 95, $icon('html5')]],
            'Tools & DevOps' => [['Git', 90, $icon('git')], ['Docker', 70, $icon('docker')], ['Linux', 75, $icon('linux')], ['Figma', 70, $icon('figma')]],
        ];
        $order = 0;
        foreach ($skills as $cat => $items) {
            foreach ($items as [$name, $level, $ic]) {
                Skill::create(['name' => $name, 'category' => $cat, 'level' => $level, 'icon' => $ic, 'sort_order' => $order++]);
            }
        }

        $projects = [
            ['ShopFlow E-commerce', 'Web App', 'A headless e-commerce platform with inventory, payments and a blazing fast storefront.', 'Laravel, Vue, Stripe, MySQL', true],
            ['TaskPilot', 'SaaS', 'Team task management SaaS with real-time boards, notifications and analytics.', 'Laravel, Livewire, Tailwind, Pusher', true],
            ['HealthTrack API', 'API', 'Secure REST API powering a fitness mobile app used by 20k+ users.', 'Laravel, Sanctum, Redis, Docker', true],
            ['Brand Studio Website', 'Website', 'Award-winning marketing site for a creative agency with smooth animations.', 'Tailwind, Alpine.js, GSAP', false],
            ['LearnHub LMS', 'Web App', 'Learning platform with courses, quizzes, certificates and video streaming.', 'Laravel, Inertia, React', false],
            ['FinDash', 'Dashboard', 'Personal finance dashboard with charts, budgets and bank sync.', 'Vue, Chart.js, Laravel', false],
        ];
        foreach ($projects as $i => [$title, $cat, $summary, $tech, $featured]) {
            Project::create([
                'title' => $title,
                'slug' => str($title)->slug(),
                'category' => $cat,
                'summary' => $summary,
                'description' => "## Overview\n\n{$summary}\n\n## What I did\n\n- Designed the architecture and database schema\n- Built the backend and admin tools\n- Crafted a responsive, accessible UI\n- Set up CI/CD and automated tests\n\n## Results\n\nThe project launched on time and improved key metrics significantly. **Replace this text from the admin dashboard.**",
                'tech_stack' => $tech,
                'live_url' => 'https://example.com',
                'repo_url' => 'https://github.com',
                'is_featured' => $featured,
                'sort_order' => $i,
            ]);
        }

        Project::where('slug', 'healthtrack-api')->update([
            'title' => 'HealthTrack App', 'type' => 'both', 'category' => 'Mobile',
            'app_store_url' => 'https://apps.apple.com', 'play_store_url' => 'https://play.google.com/store',
        ]);

        Experience::create(['type' => 'work', 'title' => 'Senior Laravel Developer', 'organization' => 'TechNova Inc.', 'location' => 'Remote', 'start_date' => '2023-03-01', 'is_current' => true,
            'description' => "Lead development of a multi-tenant SaaS used by 300+ companies\nReduced API response times by 45% with caching and query optimisation\nMentor a team of 4 developers and run code reviews"]);
        Experience::create(['type' => 'work', 'title' => 'Full-Stack Developer', 'organization' => 'PixelCraft Agency', 'location' => 'London, UK', 'start_date' => '2020-06-01', 'end_date' => '2023-02-28',
            'description' => "Built 20+ client websites and web apps with Laravel and Vue\nIntroduced automated testing and CI pipelines"]);
        Experience::create(['type' => 'work', 'title' => 'Junior Web Developer', 'organization' => 'StartUp Labs', 'start_date' => '2019-01-01', 'end_date' => '2020-05-31',
            'description' => "Developed features for an internal CRM\nMaintained WordPress and PHP sites"]);
        Experience::create(['type' => 'education', 'title' => 'BSc in Computer Science', 'organization' => 'State University', 'start_date' => '2015-09-01', 'end_date' => '2019-06-30',
            'description' => "Graduated with honours\nFinal project: real-time collaborative code editor"]);
        Experience::create(['type' => 'education', 'title' => 'AWS Certified Developer', 'organization' => 'Amazon Web Services', 'start_date' => '2022-04-01', 'end_date' => '2022-04-30']);

        Testimonial::create(['name' => 'Sarah Johnson', 'position' => 'CTO', 'company' => 'TechNova', 'rating' => 5, 'sort_order' => 1,
            'content' => 'Alex is one of the most reliable developers I have worked with. Clean code, great communication and always delivers ahead of schedule.']);
        Testimonial::create(['name' => 'Michael Chen', 'position' => 'Product Manager', 'company' => 'PixelCraft', 'rating' => 5, 'sort_order' => 2,
            'content' => 'Our client projects became faster and far easier to maintain after Alex joined. A true professional with an eye for detail.']);
        Testimonial::create(['name' => 'Emma Williams', 'position' => 'Founder', 'company' => 'LearnHub', 'rating' => 5, 'sort_order' => 3,
            'content' => 'Alex turned our rough idea into a polished platform. Highly recommended for anyone who needs a dependable Laravel expert.']);

        $posts = [
            ['10 Laravel Tips to Write Cleaner Code', 'laravel, php, tips'],
            ['Building a Modern Portfolio with Tailwind CSS', 'tailwind, css, design'],
            ['How I Optimised a Slow API by 45%', 'performance, api, laravel'],
        ];
        foreach ($posts as $i => [$title, $tags]) {
            Post::create([
                'title' => $title,
                'slug' => str($title)->slug(),
                'excerpt' => 'Practical lessons learned from real-world projects, explained step by step with examples you can use today.',
                'body' => "## Introduction\n\nThis is a sample article. Edit or delete it from **Admin → Blog**.\n\n## Key points\n\n- Keep controllers thin\n- Use form requests and policies\n- Write tests for the important paths\n\n```php\nRoute::get('/hello', fn () => 'Hello world');\n```\n\n> Good code is its own best documentation.\n\n## Conclusion\n\nSmall habits compound into big improvements over time.",
                'tags' => $tags,
                'is_published' => true,
                'published_at' => now()->subDays(($i + 1) * 6),
                'views' => rand(40, 400),
            ]);
        }

        Message::create(['name' => 'John Carter', 'email' => 'john@example.com', 'subject' => 'Project inquiry',
            'message' => "Hi Alex,\n\nI saw your portfolio and would love to discuss a Laravel project for my company. Are you available next week for a call?\n\nThanks,\nJohn"]);
    }
}
