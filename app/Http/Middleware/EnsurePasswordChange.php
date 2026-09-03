<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (!$user instanceof User || $user->password_change_required_at === null) {
            return $next($request);
        }

        return redirect()
            ->to(route('profile.show').'#update-password')
            ->with('danger', 'Your administrator requires you to choose a new password before continuing.');
    }
}
