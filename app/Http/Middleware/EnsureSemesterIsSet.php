<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureSemesterIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (current_school()->semester_id == null) {
            session()->flash('danger', 'Please set the semester before proceeding.');

            return redirect()->route('semesters.index');
        }

        return $next($request);
    }
}
