<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdminHasSchoolId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->school_id == null && request()->user()->hasRole('super-admin')) {
            session()->flash('danger', 'Please set your school of operation first.');

            return redirect()->route('schools.index');
        }

        return $next($request);
    }
}
