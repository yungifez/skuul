<?php

namespace App\Http\Controllers;

use App\Events\AccountStatusChanged;
use App\Http\Requests\RegistrationRequest;
use App\Services\AccountApplication\AccountApplicationService;
use App\Services\User\UserService;
use Auth;

class RegistrationController extends Controller
{
    /**
     * Account application service instance.
     */
    public AccountApplicationService $accountApplicationService;

    /**
     * User service instance.
     */
    public UserService $userService;

    public function __construct(AccountApplicationService $accountApplicationService, UserService $userService)
    {
        $this->accountApplicationService = $accountApplicationService;
        $this->userService = $userService;
    }

    public function registerView()
    {
        return view('auth.register');
    }

    public function register(RegistrationRequest $request)
    {
        $request['school_id'] = $request->school;

        $user = $this->userService->createUser($request);

        //assign applicant role
        $user->assignRole('applicant');

        $accountApplication = $this->accountApplicationService->createAccountApplication($user->id, $request->role);

        Auth::login($user);

        $status = 'Application Received';
        $reason = 'Application has been received, we would reach out to you for further information';
        $accountApplication->setStatus($status, $reason);

        //dispatch event
        AccountStatusChanged::dispatch($user, $status, $reason);

        return back()->with('success', 'Registration complete, you would receive an email to verify your account');
    }
}
