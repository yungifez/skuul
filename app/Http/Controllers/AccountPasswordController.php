<?php

namespace App\Http\Controllers;

use App\Actions\Identity\SetAccountPassword;
use App\Http\Requests\SetAccountPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AccountPasswordController extends Controller
{
    /**
     * Set a person's password from the account management screen.
     */
    public function update(
        User $user,
        SetAccountPasswordRequest $request,
        SetAccountPassword $setAccountPassword,
    ): RedirectResponse {
        $this->authorize('manageAccountAccess', $user);

        $setAccountPassword->set(
            user: $user,
            password: $request->validated('password'),
            forceReset: $request->boolean('force_reset'),
            actor: $request->user(),
        );

        return back()->with('success', $request->boolean('force_reset')
            ? "Set {$user->name}'s password and required a change at next sign-in."
            : "Set {$user->name}'s password.");
    }
}
