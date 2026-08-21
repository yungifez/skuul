<?php

namespace App\Http\Controllers;

use App\Actions\Identity\ChangeAccountStatus;
use App\Enums\AccountStatus;
use App\Http\Requests\ChangeAccountStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AccountStatusController extends Controller
{
    /**
     * Move an account between access states.
     */
    public function update(User $user, ChangeAccountStatusRequest $request, ChangeAccountStatus $changeAccountStatus): RedirectResponse
    {
        $this->authorize('manageAccountAccess', $user);

        $status = AccountStatus::from($request->validated('account_status'));
        $reason = $request->validated('reason');
        $actor = $request->user();

        match ($status) {
            AccountStatus::Suspended => $changeAccountStatus->suspend($user, $actor, $reason),
            AccountStatus::Archived  => $changeAccountStatus->archive($user, $actor, $reason),
            default                  => $changeAccountStatus->reinstate($user, $actor, $reason),
        };

        return back()->with('success', "Set {$user->name}'s account to {$user->account_status->label()}.");
    }
}
