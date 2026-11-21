<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use Illuminate\Database\Seeder;

/**
 * Positions the portfolio as Flutter-first, with Laravel web development as a second strength.
 * Run: php artisan db:seed --class=WebAndAppContentSeeder
 *
 * Safe to re-run. It never deletes anything: it updates the profile texts and only adds
 * the web skills, services and sample projects that don't exist yet.
 */
class WebAndAppContentSeeder extends Seeder
{
    public function run(): void
    {
        $name = setting('name', 'a developer');

        Setting::put([
            'role' => 'Senior Flutter & Laravel Developer',
            'typed_words' => 'Flutter Apps, iOS & Android, Laravel Web Apps, APIs & Admin Panels',
            'hero_intro' => 'I build fast, beautiful mobile apps with Flutter, plus the Laravel web apps, APIs and admin panels behind them, from the first sketch to App Store, Play Store and production launch.',
            'about_title' => 'Mobile-first, full-stack when it matters',
            'about' => "I'm a **Senior Flutter Developer** at heart. Most of my work is polished, high-performance iOS and Android apps with smooth 60fps animations, clean architecture and code teams can maintain for years.\n\nOn the web side I build with **Laravel and PHP**: business websites, SaaS dashboards, REST APIs and admin panels. That means I can ship the app *and* the backend that powers it, with one person owning the whole product.\n\nI work across the full lifecycle: UI from Figma, state management with **Riverpod and Bloc**, Laravel and Firebase backends, automated testing, CI/CD, store releases and server deployment.",
            'site_title' => setting('name', 'Portfolio').' · Flutter & Laravel Developer',
            'meta_description' => "Portfolio of {$name}, a Senior Flutter Developer building cross-platform iOS and Android apps, and Laravel web apps, APIs and admin panels.",
        ]);

        // ---- Skills: add a Web & Backend group right after Mobile ----------
        if (! Skill::where('name', 'Laravel')->exists()) {
            // Push non-mobile groups down so the web group sits second.
            Skill::where('category', '!=', 'Mobile')->increment('sort_order', 10);

            $icon = fn ($n, $v = 'original') => "https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/icons/{$n}/{$n}-{$v}.svg";
            $web = [
                ['Laravel', 90, $icon('laravel')],
                ['PHP', 88, $icon('php')],
                ['MySQL', 85, $icon('mysql')],
                ['Tailwind CSS', 85, $icon('tailwindcss')],
                ['REST API Design', 90, null],
            ];
            foreach ($web as $i => [$skill, $level, $ic]) {
                Skill::create(['name' => $skill, 'category' => 'Web & Backend', 'level' => $level, 'icon' => $ic, 'sort_order' => 4 + $i]);
            }
        }

        // ---- Services: 3 web services after the 6 Flutter ones -------------
        $services = [
            ['Laravel Web Apps & SaaS', 'Secure, fast web applications, SaaS dashboards and client portals built with Laravel, PHP and Tailwind CSS.', 'globe', 'Laravel, PHP, MySQL, Tailwind'],
            ['APIs & Admin Panels for Your App', 'Laravel REST APIs, authentication and admin dashboards that power your Flutter app, so mobile and web share one solid backend.', 'code', 'Laravel API, Sanctum, Admin CMS'],
            ['Business Websites & Landing Pages', 'Responsive, SEO-friendly websites with a simple CMS, so you can update content yourself without touching code.', 'monitor', 'Laravel, SEO, Responsive'],
        ];
        $next = (int) Service::max('sort_order') + 1;
        foreach ($services as [$title, $desc, $ic, $tags]) {
            if (! Service::where('title', $title)->exists()) {
                Service::create(['title' => $title, 'description' => $desc, 'icon' => $ic, 'tags' => $tags, 'sort_order' => $next++]);
            }
        }

        // ---- Projects: two sample Laravel web projects ---------------------
        // Show the Laravel backend on the cross-platform sample, if it's still untouched.
        Project::where('slug', 'shopease')->where('tech_stack', 'Flutter, Riverpod, REST API, Stripe')
            ->update(['tech_stack' => 'Flutter, Riverpod, Laravel API, Stripe']);

        $projects = [
            [
                'BizDesk CRM', 'SaaS',
                'SaaS CRM for small businesses with a leads pipeline, invoices, team roles and a live analytics dashboard.',
                'Laravel, PHP, MySQL, Tailwind CSS, Alpine.js',
            ],
            [
                'Developer Portfolio CMS', 'Web App',
                'This website: a Laravel CMS with a glassmorphism admin panel to manage projects, blog, skills, CV and messages.',
                'Laravel 13, PHP 8.5, Tailwind CSS 4, SQLite',
            ],
        ];
        $next = (int) Project::max('sort_order') + 1;
        foreach ($projects as [$title, $cat, $summary, $tech]) {
            $slug = str($title)->slug()->toString();
            if (Project::where('slug', $slug)->exists()) {
                continue;
            }
            Project::create([
                'title' => $title,
                'slug' => $slug,
                'category' => $cat,
                'type' => 'website',
                'summary' => $summary,
                'description' => "## Overview\n\n{$summary}\n\n## My role\n\n- Designed the database schema and built the backend in Laravel\n- Built a responsive UI with Tailwind CSS and Blade components\n- Added authentication, role-based access and a full admin panel\n- Deployed to production with automated backups and zero-downtime releases\n\n## Highlights\n\n- Page loads under one second\n- Clean, tested code that is easy to extend\n- Works perfectly on mobile, tablet and desktop\n\n**Replace this sample text with your real project details from the admin dashboard.**",
                'tech_stack' => $tech,
                'live_url' => 'https://example.com',
                'repo_url' => 'https://github.com',
                'is_featured' => false,
                'sort_order' => $next++,
            ]);
        }
    }
}
