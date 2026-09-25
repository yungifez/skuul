<?php

namespace App\Livewire;

use App\Actions\Staff\ManageStaffLeave as ManageStaffLeaveAction;
use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Exceptions\InvalidValueException;
use App\Models\StaffLeaveRequest as StaffLeaveRequestModel;
use App\Services\Staff\StaffLeaveBoard as StaffLeaveBoardService;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StaffLeaveBoard extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $type = '';

    public string $staffProfileId = '';

    public string $leaveType = 'annual';

    public string $startsOn = '';

    public string $endsOn = '';

    public string $reason = '';

    public ?string $feedback = null;

    protected StaffLeaveBoardService $board;

    protected ManageStaffLeaveAction $manageStaffLeave;

    public function boot(StaffLeaveBoardService $board, ManageStaffLeaveAction $manageStaffLeave): void
    {
        Gate::authorize('viewAny', StaffLeaveRequestModel::class);

        $this->board = $board;
        $this->manageStaffLeave = $manageStaffLeave;
    }

    public function mount(): void
    {
        $selectedStatus = LeaveStatus::tryFrom($this->status);
        $selectedType = LeaveType::tryFrom($this->type);
        $this->status = $selectedStatus === null ? '' : $selectedStatus->value;
        $this->type = $selectedType === null ? '' : $selectedType->value;
        $this->startsOn = now()->toDateString();
        $this->endsOn = now()->toDateString();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->type = '';
        $this->resetPage();
    }

    public function save(): void
    {
        $this->feedback = null;
        Gate::authorize('create', StaffLeaveRequestModel::class);

        $validated = $this->validate([
            'staffProfileId' => ['required', 'integer'],
            'leaveType' => ['required', Rule::enum(LeaveType::class)],
            'startsOn' => ['required', 'date'],
            'endsOn' => ['required', 'date', 'after_or_equal:startsOn'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [
            'endsOn.after_or_equal' => 'Leave cannot end before it starts.',
        ]);

        try {
            $this->manageStaffLeave->request(
                profile: $this->board->profile((int) $validated['staffProfileId']),
                startsOn: $validated['startsOn'],
                endsOn: $validated['endsOn'],
                type: LeaveType::from($validated['leaveType']),
                reason: $validated['reason'] ?: null,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('leave', $exception->getMessage());

            return;
        }

        $this->reset('staffProfileId', 'reason');
        $this->feedback = 'The leave was asked for.';
    }

    public function changeStatus(
        int $requestId,
        string $status,
    ): void {
        $this->feedback = null;
        $request = $this->board->leaveRequest($requestId);
        Gate::authorize('decide', $request);
        $nextStatus = LeaveStatus::tryFrom($status);

        if (!in_array($nextStatus, [LeaveStatus::Approved, LeaveStatus::Declined], true)) {
            abort(422);
        }

        try {
            $this->manageStaffLeave->changeStatus($request, $nextStatus, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('status', $exception->getMessage());

            return;
        }

        $this->feedback = 'The leave is now '.$nextStatus->label().'.';
    }

    public function render(): ViewContract
    {
        return View::make('livewire.staff-leave-board', [
            'leaveRequests' => $this->board->requests($this->status ?: null, $this->type ?: null),
            'statuses' => LeaveStatus::cases(),
            'types' => LeaveType::cases(),
            'profiles' => $this->board->employedProfiles(),
            'waitingCount' => $this->board->waitingCount(),
            'awayToday' => $this->board->awayToday(),
        ]);
    }
}
