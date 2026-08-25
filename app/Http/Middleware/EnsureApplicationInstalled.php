<?php

namespace App\Http\Middleware;

use App\Actions\Installation\CompleteExistingInstallation;
use App\Models\Installation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureApplicationInstalled
{
    public function __construct(private CompleteExistingInstallation $completeExistingInstallation) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if ($request->routeIs('install.*', 'health')) {
            return $next($request);
        }

        try {
            if (!Schema::hasTable('installations')) {
                return redirect()->route('install.index');
            }

            $this->completeExistingInstallation->recordIfReady();

            if (Installation::isInstalled()) {
                return $next($request);
            }
        } catch (Throwable) {
            return redirect()->route('install.index');
        }

        return redirect()->route('install.index');
    }
}
