<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\User\UserService;
use Spatie\Permission\Models\Role;
use App\Services\AccountApplication\AccountApplicationService;

class RegistrationController extends Controller
{
    /**
     * Account application service instance
     *
     * @var AccountApplicationService $accountApplicationService
     */
    public AccountApplicationService $accountApplicationService;
    
    /**
     * User service instance
     *
     * @var UserService
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

    public function register(Request $request)
    {
        $roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in($roles->pluck('id')),
            ],
            'school' => [
                'required',
                'exists:schools,id',
            ],
        ]);

        $request['school_id'] = $request->school;

        $user = $this->userService->createUser($request);

        //assign applicant role
        $user->assignRole('applicant');

        $accountApplication = $this->accountApplicationService->createAccountApplication($user->id, $request->role);

        $accountApplication->setStatus('Application Recieved', 'Application has been recieved, we would reach out to you for further information');

        session()->flash('success', 'Registration complete, you would recieve an email to verify your account');
        return back();
    }
}
