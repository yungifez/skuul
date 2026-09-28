<?php

namespace App\Livewire;

use App\Actions\Staff\ManageStaffProfile;
use App\Enums\EmploymentType;
use App\Enums\StaffStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\StaffProfile;
use App\Services\Staff\StaffAvailability as StaffAvailabilityService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * One person's employment record: the job, what they are qualified for, the
 * hours they work, and the leave they asked for.
 */
class StaffProfileRecord extends Component
{
    use DispatchesStatusNotifications;

    public const array DAYS = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];

    #[Locked]
    public StaffProfile $profile;

    public bool $isEditingJob = false;

    public string $jobTitle = '';

    public string $department = '';

    public string $staffNumber = '';

    public string $employmentType = '';

    public string $status = '';

    public string $leftOn = '';

    public string $credentialType = '';

    public string $credentialName = '';

    public string $credentialIssuer = '';

    public string $credentialIssuedOn = '';

    public string $credentialExpiresOn = '';

    public string $dayOfWeek = '1';

    public string $startsAt = '';

    public string $endsAt = '';

    public function mount(StaffProfile $profile): void
    {
        Gate::authorize('view', $profile);

        $this->profile = $profile;
    }

    public function startEditingJob(): void
    {
        Gate::authorize('update', $this->profile);

        $profile = $this->profile->fresh() ?? $this->profile;
        $this->isEditingJob = true;
        $this->jobTitle = (string) $profile->job_title;
        $this->department = (string) $profile->department;
        $this->staffNumber = (string) $profile->staff_number;
        $this->employmentType = $profile->employment_type->value;
        $this->status = $profile->status->value;
        $this->leftOn = (string) $profile->left_on?->toDateString();
    }

    public function stopEditingJob(): void
    {
        $this->isEditingJob = false;
        $this->resetValidation();
    }

    public function saveJob(ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('update', $this->profile);

        foreach (['jobTitle', 'department', 'staffNumber'] as $field) {
            $this->{$field} = trim($this->{$field});
        }

        $this->validate([
            'jobTitle' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'staffNumber' => ['nullable', 'string', 'max:30'],
            'employmentType' => ['required', Rule::enum(EmploymentType::class)],
            'status' => ['required', Rule::enum(StaffStatus::class)],
            'leftOn' => ['nullable', 'date'],
        ], [], ['leftOn' => 'leaving date']);

        try {
            $manageStaffProfile->update($this->profile, [
                'job_title' => $this->jobTitle === '' ? null : $this->jobTitle,
                'department' => $this->department === '' ? null : $this->department,
                'staff_number' => $this->staffNumber === '' ? null : $this->staffNumber,
                'employment_type' => $this->employmentType,
                'status' => $this->status,
                'left_on' => $this->leftOn === '' ? null : $this->leftOn,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $field = match (true) {
                str_contains($exception->getMessage(), 'staff number') => 'staffNumber',
                str_contains($exception->getMessage(), 'learner') => 'status',
                default => 'leftOn',
            };

            $this->addError($field, $exception->getMessage());

            return;
        }

        $this->profile->refresh();
        $this->isEditingJob = false;
        $this->notify('The employment record was saved.');
    }

    public function addCredential(ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('update', $this->profile);

        $validated = $this->validate([
            'credentialType' => ['required', 'string', 'max:50'],
            'credentialName' => ['required', 'string', 'max:150'],
            'credentialIssuer' => ['nullable', 'string', 'max:150'],
            'credentialIssuedOn' => ['nullable', 'date'],
            'credentialExpiresOn' => ['nullable', 'date', 'after_or_equal:credentialIssuedOn'],
        ], [], [
            'credentialType' => 'kind',
            'credentialName' => 'name',
            'credentialIssuer' => 'issuer',
            'credentialIssuedOn' => 'issue date',
            'credentialExpiresOn' => 'end date',
        ]);

        $manageStaffProfile->addCredential($this->profile, [
            'type' => trim($validated['credentialType']),
            'name' => trim($validated['credentialName']),
            'issuer' => trim($this->credentialIssuer) === '' ? null : trim($this->credentialIssuer),
            'issued_on' => $this->credentialIssuedOn === '' ? null : $this->credentialIssuedOn,
            'expires_on' => $this->credentialExpiresOn === '' ? null : $this->credentialExpiresOn,
        ], auth()->user());

        $this->reset('credentialType', 'credentialName', 'credentialIssuer', 'credentialIssuedOn', 'credentialExpiresOn');
        $this->notify('The qualification was added.');
    }

    public function removeCredential(int $credentialId, ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('update', $this->profile);

        $manageStaffProfile->removeCredential($this->profile->credentials()->findOrFail($credentialId), auth()->user());
        $this->notify('The qualification was removed.');
    }

    public function addHours(ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('update', $this->profile);

        $this->validate([
            'dayOfWeek' => ['required', 'integer', 'between:1,7'],
            'startsAt' => ['required', 'date_format:H:i'],
            'endsAt' => ['required', 'date_format:H:i', 'after:startsAt'],
        ], [
            'endsAt.after' => 'The hours must end after they start.',
        ], ['dayOfWeek' => 'day', 'startsAt' => 'start', 'endsAt' => 'end']);

        try {
            $manageStaffProfile->addHours($this->profile, (int) $this->dayOfWeek, $this->startsAt, $this->endsAt, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('startsAt', $exception->getMessage());

            return;
        }

        $this->reset('startsAt', 'endsAt');
        $this->notify('The working hours were added.');
    }

    public function removeHours(int $hoursId, ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('update', $this->profile);

        $manageStaffProfile->removeHours($this->profile->availabilities()->findOrFail($hoursId), auth()->user());
        $this->notify('The working hours were removed.');
    }

    public function render(StaffAvailabilityService $availability): View
    {
        $this->profile->load([
            'user:id,name,email',
            'credentials',
            'availabilities' => fn ($query) => $query->orderBy('day_of_week')->orderBy('starts_at'),
            'leaveRequests' => fn ($query) => $query->orderByDesc('starts_on'),
        ]);

        return view('livewire.staff-profile-record', [
            'canWrite' => Gate::allows('update', $this->profile),
            'isAway' => $availability->isAway($this->profile, now()),
            'employmentTypes' => EmploymentType::cases(),
            'statuses' => StaffStatus::cases(),
        ]);
    }
}
