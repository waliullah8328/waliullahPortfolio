<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Switches the portfolio's starter content to a Senior Flutter Developer profile.
 * Run: php artisan db:seed --class=FlutterContentSeeder
 *
 * Keeps personal data (name, photo, contact, links, stats, CV, messages) and
 * replaces only the original demo content. Real experience entries are kept.
 */
class FlutterContentSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put([
            'role' => 'Senior Flutter Developer',
            'typed_words' => 'Cross-platform Apps, Pixel-perfect UI, Clean Architecture, iOS & Android',
            'hero_intro' => 'I craft fast, beautiful and scalable mobile apps for iOS, Android and the web from a single Flutter codebase, from the first sketch to App Store and Play Store launch.',
            'about_title' => 'Building apps people love to use',
            'about' => "I'm a **Senior Flutter Developer** who turns ideas into polished, high-performance mobile apps. I care about smooth 60fps animations, clean architecture and code that teams can maintain for years.\n\nI work across the whole lifecycle: UI implementation from Figma, state management with **Riverpod and Bloc**, Firebase and REST/GraphQL backends, automated testing, CI/CD and store releases.\n\nI also mentor developers, review code and help teams adopt best practices.",
            'site_title' => setting('name', 'Portfolio').' · Senior Flutter Developer',
            'meta_description' => 'Portfolio of '.setting('name', 'a developer').', a Senior Flutter Developer building cross-platform iOS, Android and web apps with Flutter and Dart.',
            'show_services' => '1',
        ]);

        // ---- Skills -------------------------------------------------------
        Skill::query()->delete();
        $icon = fn ($n, $v = 'original') => "https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/icons/{$n}/{$n}-{$v}.svg";
        $skills = [
            'Mobile' => [['Flutter', 96, $icon('flutter')], ['Dart', 95, $icon('dart')], ['Kotlin', 72, $icon('kotlin')], ['Swift', 65, $icon('swift')]],
            'State & Architecture' => [['Riverpod', 92, null], ['Bloc / Cubit', 90, null], ['Provider', 88, null], ['Clean Architecture', 90, null]],
            'Backend & Data' => [['Firebase', 92, $icon('firebase')], ['REST APIs & Dio', 92, null], ['GraphQL', 75, $icon('graphql', 'plain')], ['Supabase', 72, $icon('supabase')]],
            'Tools & DevOps' => [['Git', 90, $icon('git')], ['GitHub Actions', 80, $icon('githubactions')], ['Figma', 82, $icon('figma')]],
        ];
        $order = 0;
        foreach ($skills as $cat => $items) {
            foreach ($items as [$name, $level, $ic]) {
                Skill::create(['name' => $name, 'category' => $cat, 'level' => $level, 'icon' => $ic, 'sort_order' => $order++]);
            }
        }

        // ---- Services -----------------------------------------------------
        if (! Service::exists()) {
            $services = [
                ['Cross-platform App Development', 'Native-quality iOS and Android apps from one Flutter codebase, so you ship faster and maintain less.', 'smartphone', 'Flutter, Dart, iOS, Android'],
                ['Pixel-perfect UI & Animations', 'Figma designs turned into smooth 60fps interfaces with custom widgets, micro-interactions and motion.', 'palette', 'Figma, Animations, Material 3'],
                ['Scalable Architecture', 'Clean Architecture with Riverpod or Bloc: testable, modular code that grows with your product and team.', 'layers', 'Riverpod, Bloc, Clean Arch'],
                ['Backend & API Integration', 'Firebase, Supabase, REST and GraphQL integrations with offline caching, real-time sync and push notifications.', 'database', 'Firebase, REST, GraphQL'],
                ['Performance & Quality', 'Profiling, crash reduction, unit, widget and integration tests, so your app stays fast and reliable.', 'zap', 'DevTools, Testing, Crashlytics'],
                ['Store Release & CI/CD', 'Automated builds, signing and deployment to the App Store and Google Play, plus post-launch support.', 'rocket', 'App Store, Play Store, CI/CD'],
            ];
            foreach ($services as $i => [$title, $desc, $ic, $tags]) {
                Service::create(['title' => $title, 'description' => $desc, 'icon' => $ic, 'tags' => $tags, 'sort_order' => $i]);
            }
        }

        // ---- Projects (replace original demo projects only) --------------
        Project::whereIn('slug', ['shopflow-e-commerce', 'taskpilot', 'healthtrack-api', 'brand-studio-website', 'learnhub-lms', 'findash'])
            ->whereNull('image')->delete();

        $ios = 'https://apps.apple.com';
        $play = 'https://play.google.com/store';
        $projects = [
            ['FoodieGo', 'Delivery', 'app', true, 'Food delivery app with live order tracking, Google Maps, secure payments and real-time push notifications.', 'Flutter, Riverpod, Firebase, Google Maps, Stripe', $ios, $play],
            ['FitPulse', 'Health & Fitness', 'app', true, 'Fitness tracker with workout plans, HealthKit/Google Fit sync, progress charts and offline mode.', 'Flutter, Bloc, Hive, HealthKit, Charts', $ios, $play],
            ['ShopEase', 'E-commerce', 'both', true, 'Multi-vendor shopping app for iOS, Android and web with cart, wishlist, coupons and order history.', 'Flutter, Riverpod, REST API, Stripe', $ios, $play],
            ['ChatNest', 'Social', 'app', false, 'Real-time messenger with 1:1 and group chats, voice notes, media sharing and end-to-end encryption.', 'Flutter, Firebase, FCM, WebRTC', $ios, $play],
            ['EduLearn', 'Education', 'both', false, 'E-learning platform with video courses, quizzes, certificates and downloadable lessons for offline study.', 'Flutter, Supabase, Riverpod, Video Player', null, $play],
            ['WalletX', 'Fintech', 'app', false, 'Personal finance app with budgets, expense categories, biometric login and beautiful animated charts.', 'Flutter, Bloc, SQLite, Local Auth', $ios, $play],
            ['Flutter UI Kit', 'Open Source', 'website', false, 'Open-source package of 40+ customizable, animated Flutter widgets published on pub.dev.', 'Flutter, Dart, pub.dev', null, null],
        ];
        foreach ($projects as $i => [$title, $cat, $type, $featured, $summary, $tech, $appStore, $playStore]) {
            $slug = str($title)->slug()->toString();
            if (Project::where('slug', $slug)->exists()) {
                continue;
            }
            Project::create([
                'title' => $title,
                'slug' => $slug,
                'category' => $cat,
                'type' => $type,
                'summary' => $summary,
                'description' => "## Overview\n\n{$summary}\n\n## My role\n\n- Built the app end-to-end in Flutter with a Clean Architecture structure\n- Implemented state management, API integration and offline caching\n- Crafted pixel-perfect UI and smooth animations from Figma designs\n- Set up automated tests and CI/CD for App Store and Play Store releases\n\n## Highlights\n\n- 60fps animations and fast startup time\n- Crash-free sessions above 99.5%\n- One codebase for iOS, Android".($type === 'both' ? ' and web' : '')."\n\n**Replace this sample text with your real project details from the admin dashboard.**",
                'tech_stack' => $tech,
                'live_url' => $type === 'app' ? null : 'https://example.com',
                'repo_url' => 'https://github.com',
                'app_store_url' => $type === 'website' ? null : $appStore,
                'play_store_url' => $type === 'website' ? null : $playStore,
                'is_featured' => $featured,
                'sort_order' => $i,
            ]);
        }

        // ---- Experience: remove only the original demo entries ------------
        Experience::whereIn('organization', ['PixelCraft Agency', 'StartUp Labs', 'State University', 'Amazon Web Services'])->delete();

        // ---- Recommendations: remove demo quotes (they named a demo person) --
        Testimonial::whereIn('name', ['Sarah Johnson', 'Michael Chen', 'Emma Williams'])->delete();

        // ---- Blog: replace demo Laravel posts with Flutter articles -------
        Post::whereIn('slug', ['10-laravel-tips-to-write-cleaner-code', 'building-a-modern-portfolio-with-tailwind-css', 'how-i-optimised-a-slow-api-by-45'])
            ->whereNull('cover_image')->delete();

        $posts = [
            [
                'Riverpod vs Bloc: Choosing State Management in 2026',
                'A practical comparison of Riverpod and Bloc for real-world Flutter apps, and how to pick the right one for your team.',
                'flutter, riverpod, bloc, state-management',
                "## Why it matters\n\nState management shapes how your whole Flutter app is structured. Pick well and features are easy to add; pick badly and every change hurts.\n\n## Riverpod\n\n- Compile-time safe, no `BuildContext` needed\n- Great for dependency injection and caching\n- Less boilerplate with code generation\n\n```dart\n@riverpod\nFuture<List<Product>> products(Ref ref) async {\n  return ref.watch(apiProvider).fetchProducts();\n}\n```\n\n## Bloc\n\n- Explicit events and states, very predictable\n- Excellent tooling and testing story\n- Fits large teams that like strict structure\n\n## My rule of thumb\n\n> Use **Riverpod** for most product apps; reach for **Bloc** when the team wants strict event-driven flows.\n\nBoth work great with Clean Architecture. Consistency matters more than the library.",
            ],
            [
                '10 Tips to Make Your Flutter App Feel Buttery Smooth',
                'Simple, proven techniques to remove jank, cut rebuilds and keep your Flutter UI running at 60fps or higher.',
                'flutter, performance, tips',
                "## 1. Use `const` constructors\n\nConst widgets are built once and reused, which saves work on every frame.\n\n## 2. Keep `build()` cheap\n\nNever do heavy work, parsing or I/O inside `build`.\n\n## 3. Split big widgets\n\nSmaller widgets mean smaller rebuilds. Use `select` (Riverpod) or `buildWhen` (Bloc).\n\n## 4. Use `ListView.builder`\n\nLazily build long lists instead of creating every child up front.\n\n## 5. Cache images\n\n```dart\nCachedNetworkImage(imageUrl: url, memCacheWidth: 400)\n```\n\n## 6. Profile in profile mode\n\nDebug mode is slow by design. Always measure with `flutter run --profile` and DevTools.\n\n## 7-10\n\n- Avoid `Opacity` in animations, use `FadeTransition`\n- Use `RepaintBoundary` for complex static parts\n- Move heavy work to isolates with `compute`\n- Precache images on splash\n\nSmall habits compound into a noticeably smoother app.",
            ],
            [
                'Clean Architecture in Flutter: A Practical Guide',
                'How I structure production Flutter apps into data, domain and presentation layers so they stay testable and easy to grow.',
                'flutter, architecture, clean-code',
                "## The three layers\n\n- **Presentation**: widgets plus state (Riverpod/Bloc)\n- **Domain**: entities, use cases and repository contracts in pure Dart\n- **Data**: API clients, local storage and repository implementations\n\n## Folder structure\n\n```\nlib/\n  features/\n    auth/\n      data/\n      domain/\n      presentation/\n  core/\n```\n\n## Why feature-first?\n\nEach feature is self-contained, so teams can work in parallel and delete features cleanly.\n\n## Testing becomes easy\n\nBecause the domain layer is pure Dart, use cases can be unit tested without Flutter at all.\n\n> Architecture is not about folders. It's about making change cheap.",
            ],
        ];
        foreach ($posts as $i => [$title, $excerpt, $tags, $body]) {
            $slug = str($title)->slug()->toString();
            if (Post::where('slug', $slug)->exists()) {
                continue;
            }
            Post::create([
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body' => $body,
                'tags' => $tags,
                'is_published' => true,
                'published_at' => now()->subDays(($i + 1) * 5),
            ]);
        }
    }
}
