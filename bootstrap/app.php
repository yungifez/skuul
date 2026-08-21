<?php

use App\Exceptions\ApplicationException;
use App\Http\Middleware\SetActiveSchool;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware
            ->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_AWS_ELB)
            ->trimStrings(['current_password', 'password', 'password_confirmation'])
            ->redirectGuestsTo(fn (): string => route('login'))
            ->redirectUsersTo(fn (): string => config('fortify.home'))
            ->throttleApi()
            ->authenticateSessions()
            // Resolve the school being worked in for every signed-in web request.
            ->appendToGroup('web', [
                SetActiveSchool::class,
            ])
            ->alias([
                'role'               => RoleMiddleware::class,
                'permission'         => PermissionMiddleware::class,
                'role_or_permission' => RoleOrPermissionMiddleware::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions
            ->dontReport([ApplicationException::class])
            ->dontFlash(['current_password', 'password', 'password_confirmation']);
    })
    ->create();
