<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Hosts like Vercel terminate HTTPS at their proxy; trust its X-Forwarded-* headers
        // so generated URLs, redirects and secure cookies use https.
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route("admin.login"));
        $middleware->redirectUsersTo(fn () => route("admin.dashboard"));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Public forms (contact / recommendation) posted without JavaScript: instead of a bare
        // 429 / 419 error page, go back to the form and explain it in a toast.
        $backToForm = function (Request $request, string $message) {
            if ($request->expectsJson() || ! $request->routeIs('contact.store', 'recommend.store')) {
                return null;
            }
            $anchor = $request->routeIs('contact.store') ? '#contact' : '#testimonials';

            return redirect()->to(url()->previous().$anchor)->withInput($request->except('_token'))->with('toast_error', $message);
        };
        $exceptions->render(fn (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) => $backToForm($request, 'You are sending too fast. Please wait a minute and try again.'));
        $exceptions->render(fn (\Illuminate\Session\TokenMismatchException $e, Request $request) => $backToForm($request, 'Your session expired. Please try sending again.'));

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
