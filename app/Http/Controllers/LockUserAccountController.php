<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\User\UserService;
use App\Http\Requests\LockUserAccountRequest;

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

        return back()->with('success', ($lock == true ? "Locked" : "Unlocked")." {$user->name}'s account successfully");
    }
}
