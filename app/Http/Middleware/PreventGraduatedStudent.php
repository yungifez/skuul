<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreventGraduatedStudent
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user instanceof User || !$user->hasRole(Role::Student)) {
            return $next($request);
        }

        if ($user->studentRecord?->isGraduated()) {
            session()->flash('danger', 'You cannot access this resource because you have been marked as graduated');

            return redirect('dashboard');
        }

        return $next($request);
    }
}
