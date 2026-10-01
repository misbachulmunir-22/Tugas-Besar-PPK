<?php

use App\Http\Middleware\EnsureAccountVerified;
use App\Http\Middleware\RoleMiddleware;
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
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if ($user) {
                if ($user->isAdmin()) {
                    return route('admin.dashboard');
                }
                if ($user->isPetugas()) {
                    return route('petugas.dashboard');
                }
                if ($user->isPengguna()) {
                    return route('pengguna.dashboard');
                }
            }

            return route('home');
        });

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'verified_account' => EnsureAccountVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
