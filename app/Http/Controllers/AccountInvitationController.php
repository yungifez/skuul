<?php

namespace App\Http\Controllers;

use App\Actions\Identity\AcceptAccountInvitation;
use App\Actions\Identity\RevokeAccountInvitation;
use App\Actions\Identity\SendAccountInvitation;
use App\Http\Requests\AcceptAccountInvitationRequest;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AccountInvitationController extends Controller
{
    /**
     * Show the invitations this administrator is responsible for.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', AccountInvitation::class);

        return view('pages.account-invitation.index');
    }

    /**
     * Show the screen where an invited person sets a password.
     */
    public function show(string $token, AcceptAccountInvitation $acceptAccountInvitation): View
    {
        $invitation = $acceptAccountInvitation->findPendingInvitation($token);

        abort_if($invitation === null, 404, 'This invitation link is not valid, or it expired.');

        return view('auth.accept-invitation', [
            'token' => $token,
            'user' => $invitation->user,
        ]);
    }

    /**
     * Set the password, activate the account, and sign the person in.
     */
    public function accept(
        string $token,
        AcceptAccountInvitationRequest $request,
        AcceptAccountInvitation $acceptAccountInvitation,
    ): RedirectResponse {
        $user = $acceptAccountInvitation->accept($token, $request->only(['password', 'password_confirmation']));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Your account is ready. Welcome to '.config('app.name').'.');
    }

    /**
     * Send or resend an invitation to a provisioned account.
     */
    public function send(User $user, SendAccountInvitation $sendAccountInvitation): RedirectResponse
    {
        $this->authorize('manageAccountAccess', $user);

        $sendAccountInvitation->send($user, auth()->user());

        return back()->with('success', "Sent an invitation to {$user->email}.");
    }

    /**
     * Stop every unused invitation link for an account.
     */
    public function revoke(User $user, RevokeAccountInvitation $revokeAccountInvitation): RedirectResponse
    {
        $this->authorize('manageAccountAccess', $user);

        $revoked = $revokeAccountInvitation->revoke($user, auth()->user());

        return back()->with('success', $revoked > 0
            ? "Revoked the invitation for {$user->name}."
            : "{$user->name} has no invitation to revoke.");
    }
}
