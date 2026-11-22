<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/*
| Public portfolio
*/
Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/projects', 'projects')->name('projects.index');
    Route::get('/projects/{project}', 'project')->name('projects.show');
    Route::get('/blog', 'blog')->name('blog.index');
    Route::get('/blog/{post}', 'post')->name('blog.show');
    Route::get('/cv', 'cv')->name('cv');
    Route::post('/contact', 'contact')->middleware('throttle:5,1')->name('contact.store');
    Route::post('/recommend', 'recommend')->middleware('throttle:3,10')->name('recommend.store');
});

/*
| Admin dashboard
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:6,1');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('account', [Admin\AuthController::class, 'account'])->name('account');
        Route::put('account', [Admin\AuthController::class, 'updateAccount']);

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings');
        Route::post('settings', [Admin\SettingController::class, 'update']);

        Route::resource('skills', Admin\SkillController::class)->except('show');
        Route::resource('services', Admin\ServiceController::class)->except('show');
        Route::resource('categories', Admin\ProjectCategoryController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['categories' => 'category']);
        Route::resource('projects', Admin\ProjectController::class)->except('show')->scoped(['project' => 'id']);
        Route::resource('experiences', Admin\ExperienceController::class)->except('show');
        Route::resource('certificates', Admin\CertificateController::class)->except('show');
        Route::post('posts/preview', [Admin\PostController::class, 'preview'])->name('posts.preview');
        Route::resource('posts', Admin\PostController::class)->except('show')->scoped(['post' => 'id']);
        Route::patch('testimonials/{testimonial}/toggle', [Admin\TestimonialController::class, 'toggle'])->name('testimonials.toggle');
        Route::resource('testimonials', Admin\TestimonialController::class)->except('show');
        Route::patch('messages/{message}/toggle', [Admin\MessageController::class, 'toggle'])->name('messages.toggle');
        Route::resource('messages', Admin\MessageController::class)->only(['index', 'show', 'destroy']);
    });
});
