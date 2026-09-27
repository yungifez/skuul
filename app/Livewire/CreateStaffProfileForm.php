<?php

namespace App\Livewire;

use App\Actions\Staff\ManageStaffProfile;
use App\Enums\EmploymentType;
use App\Exceptions\InvalidValueException;
use App\Models\StaffProfile;
use App\Traits\ListsSchoolPeople;
use App\Traits\ValidatesSchoolMembership;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Write the employment record of somebody who already works in the school.
 */
class CreateStaffProfileForm extends Component
{
    use ListsSchoolPeople;
    use ValidatesSchoolMembership;

    public string $userId = '';

    public string $staffNumber = '';

    public string $jobTitle = '';

    public string $department = '';

    public string $employmentType = '';

    public string $joinedOn = '';

    public function mount(): void
    {
        Gate::authorize('create', StaffProfile::class);

        $this->employmentType = EmploymentType::FullTime->value;
        $this->joinedOn = now()->toDateString();
    }

    public function save(ManageStaffProfile $manageStaffProfile): void
    {
        Gate::authorize('create', StaffProfile::class);

        foreach (['staffNumber', 'jobTitle', 'department'] as $field) {
            $this->{$field} = trim($this->{$field});
        }

        $this->validate([
            'userId' => ['required', 'integer', $this->memberOfWorkingSchool()],
            'staffNumber' => ['nullable', 'string', 'max:30'],
            'jobTitle' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'employmentType' => ['required', Rule::enum(EmploymentType::class)],
            'joinedOn' => ['nullable', 'date'],
        ], [
            'userId.exists' => 'Choose somebody who works in this school.',
        ], [
            'userId' => 'person',
        ]);

        try {
            $profile = $manageStaffProfile->create([
                'user_id' => (int) $this->userId,
                'staff_number' => $this->staffNumber === '' ? null : $this->staffNumber,
                'job_title' => $this->jobTitle === '' ? null : $this->jobTitle,
                'department' => $this->department === '' ? null : $this->department,
                'employment_type' => $this->employmentType,
                'joined_on' => $this->joinedOn === '' ? null : $this->joinedOn,
            ], auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError(str_contains($exception->getMessage(), 'staff number') ? 'staffNumber' : 'userId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The employment record was saved.');
        $this->redirectRoute('staff-profiles.show', $profile);
    }

    public function render(): View
    {
        return view('livewire.create-staff-profile-form', [
            'people' => $this->schoolStaff(),
            'employmentTypes' => EmploymentType::cases(),
        ]);
    }
}
