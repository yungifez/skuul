<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountApplicationStatusChangeRequest;
use App\Http\Requests\UpdateAccountApplicationRequest;
use App\Models\User;
use App\Services\AccountApplication\AccountApplicationService;
use App\Services\User\UserService;

class AccountApplicationController extends Controller
{
    /**
     * Contains account service instance.
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', [User::class, 'applicant']);

        return view('pages.account-application.index');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $applicant)
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('view', [$applicant, 'applicant']);
        $data['applicant'] = $applicant;

        return view('pages.account-application.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $applicant)
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('update', [$applicant, 'applicant']);
        $data['applicant'] = $applicant;

        return view('pages.account-application.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountApplicationRequest $request, User $applicant)
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('update', [$applicant, 'applicant']);
        $data = $request->except('_method', '_token');
        $this->accountApplicationService->updateAccountApplication($applicant, $data);

        return back()->with('success', 'Application records updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $applicant)
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('delete', [$applicant, 'applicant']);

        $this->accountApplicationService->deleteAccountApplicant($applicant);

        return back()->with('success', 'Account Application Deleted Successfully');
    }

    /**
     * View for changing application status.
     *
     * @return void
     */
    public function changeStatusView(User $applicant)
    {
        $data['applicant'] = $applicant;

        return view('pages.account-application.change-status', $data);
    }

    /**
     * Change Application Status.
     *
     * @param Request $request
     *
     * @return void
     */
    public function changeStatus(User $applicant, AccountApplicationStatusChangeRequest $request)
    {
        $data = $request->validated();
        $this->accountApplicationService->changeStatus($applicant, $data);

        return back()->with('success', 'Application status updated successfully');
    }

    /**
     * View rejected applications.
     *
     * @return void
     */
    public function rejectedApplicationsView()
    {
        return view('pages.account-application.rejected-applications');
    }
}
