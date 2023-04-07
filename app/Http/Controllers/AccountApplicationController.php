<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountApplicationStatusChangeRequest;
use App\Http\Requests\UpdateAccountApplicationRequest;
use App\Models\User;
use App\Services\AccountApplication\AccountApplicationService;
use App\Services\User\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
     */
    public function index(): View
    {
        $this->authorize('viewAny', [User::class, 'applicant']);

        return view('pages.account-application.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $applicant): View
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('view', [$applicant, 'applicant']);
        $data['applicant'] = $applicant;

        return view('pages.account-application.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $applicant): View
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('update', [$applicant, 'applicant']);
        $data['applicant'] = $applicant;

        return view('pages.account-application.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountApplicationRequest $request, User $applicant): RedirectResponse
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('update', [$applicant, 'applicant']);
        $data = $request->except('_method', '_token');
        $this->accountApplicationService->updateAccountApplication($applicant, $data);

        return back()->with('success', 'Application records updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $applicant): RedirectResponse
    {
        $this->userService->verifyUserIsOfRoleElseNotFound($applicant, 'applicant');
        $this->authorize('delete', [$applicant, 'applicant']);

        $this->accountApplicationService->deleteAccountApplicant($applicant);

        return back()->with('success', 'Account Application Deleted Successfully');
    }

    /**
     * View for changing application status.
     */
    public function changeStatusView(User $applicant): View
    {
        $data['applicant'] = $applicant;

        return view('pages.account-application.change-status', $data);
    }

    /**
     * Change Application Status.
     *
     * @param AccountApplicationStatusChangeRequest $request
     */
    public function changeStatus(User $applicant, AccountApplicationStatusChangeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->accountApplicationService->changeStatus($applicant, $data);

        return back()->with('success', 'Application status updated successfully');
    }

    /**
     * View rejected applications.
     */
    public function rejectedApplicationsView(): View
    {
        return view('pages.account-application.rejected-applications');
    }
}
