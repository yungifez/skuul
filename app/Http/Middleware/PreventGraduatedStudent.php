<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreventGraduatedStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->hasRole(Role::Student)) {
            return $next($request);
        }
        if (auth()->user()->studentRecord()->withoutGlobalScopes()->first()->is_graduated == true) {
            session()->flash('danger', 'You cannot access this resource because you have been marked as graduated');

            return redirect('dashboard');
        }

        return $next($request);
    }
}
