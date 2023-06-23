<?php

namespace App\Http\Controllers;

use App\Http\Requests\LockUserAccountRequest;
use App\Models\User;
use App\Services\User\UserService;

class LockUserAccountController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user, UserService $userService, LockUserAccountRequest $request)
    {
        $this->authorize('lockAccount', [$user]);

        $lock = $request->lock;

        $userService->lockUserAccount($user, $lock);

        return back()->with('success', ($lock == true ? 'Locked' : 'Unlocked')." {$user->name}'s account successfully");
    }
}
