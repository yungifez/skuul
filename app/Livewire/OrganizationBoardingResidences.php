<?php

namespace App\Livewire;

use App\Actions\Boarding\AttachDormitoryToBoardingResidence;
use App\Actions\Boarding\CreateBoardingResidence;
use App\Actions\Boarding\LinkSchoolToBoardingResidence;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\BoardingResidence;
use App\Models\Dormitory;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * The physical boarding sites more than one campus of an organization uses.
 *
 * Student records, rooms, beds and staff permissions stay with each campus.
 * The residence only says which houses stand on one site.
 */
class OrganizationBoardingResidences extends Component
{
    use DispatchesStatusNotifications;

    #[Locked]
    public Organization $organization;

    public string $name = '';

    public string $notes = '';

    public function mount(Organization $organization): void
    {
        Gate::authorize('manageCampuses', $organization);

        $this->organization = $organization;
    }

    public function createResidence(CreateBoardingResidence $createBoardingResidence): void
    {
        Gate::authorize('manageCampuses', $this->organization);

        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $residence = $createBoardingResidence->create($this->organization, $this->name, $this->notes, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->addError('name', $exception->getMessage());

            return;
        }

        $this->reset('name', 'notes');
        $this->notify("{$residence->name} is ready to share between campuses.");
    }

    public function linkCampus(int $residenceId, string $schoolId, LinkSchoolToBoardingResidence $linkSchoolToBoardingResidence): void
    {
        $residence = $this->residence($residenceId);

        if ($schoolId === '') {
            return;
        }

        $school = $this->organization->schools()->findOrFail((int) $schoolId);

        $this->attempt(
            fn () => $linkSchoolToBoardingResidence->link($residence, $school, auth()->user()),
            "{$school->name} can now use {$residence->name}.",
        );
    }

    public function unlinkCampus(int $residenceId, int $schoolId, LinkSchoolToBoardingResidence $linkSchoolToBoardingResidence): void
    {
        $residence = $this->residence($residenceId);
        $school = $this->organization->schools()->findOrFail($schoolId);

        $this->attempt(
            fn () => $linkSchoolToBoardingResidence->unlink($residence, $school, auth()->user()),
            "{$school->name} no longer uses {$residence->name}.",
        );
    }

    public function attachHouse(int $residenceId, string $dormitoryId, AttachDormitoryToBoardingResidence $attachDormitoryToBoardingResidence): void
    {
        $residence = $this->residence($residenceId);

        if ($dormitoryId === '') {
            return;
        }

        $dormitory = $this->house((int) $dormitoryId);

        $this->attempt(
            fn () => $attachDormitoryToBoardingResidence->attach($residence, $dormitory, auth()->user()),
            "{$dormitory->name} is now in {$residence->name}.",
        );
    }

    public function detachHouse(int $residenceId, int $dormitoryId, AttachDormitoryToBoardingResidence $attachDormitoryToBoardingResidence): void
    {
        $residence = $this->residence($residenceId);
        $dormitory = $this->house($dormitoryId);

        $this->attempt(
            fn () => $attachDormitoryToBoardingResidence->detach($residence, $dormitory, auth()->user()),
            "{$dormitory->name} is no longer in {$residence->name}.",
        );
    }

    public function render(): View
    {
        return view('livewire.organization-boarding-residences', [
            'campuses' => $this->organization->schools()->orderBy('name')->get(['id', 'name']),
            'residences' => $this->organization->boardingResidences()
                ->with([
                    'schools' => fn ($query) => $query->orderBy('name'),
                    'dormitories' => fn ($query) => $query->with('school:id,name')->orderBy('name'),
                ])
                ->orderBy('name')
                ->get(),
            'availableHouses' => $this->houses()
                ->whereNull('boarding_residence_id')
                ->with('school:id,name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Run a change and say how it went.
     */
    private function attempt(callable $change, string $success): void
    {
        try {
            $change();
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        $this->notify($success);
    }

    private function residence(int $residenceId): BoardingResidence
    {
        Gate::authorize('manageCampuses', $this->organization);

        return $this->organization->boardingResidences()->findOrFail($residenceId);
    }

    private function house(int $dormitoryId): Dormitory
    {
        return $this->houses()->findOrFail($dormitoryId);
    }

    /**
     * @return Builder<Dormitory>
     */
    private function houses(): Builder
    {
        return Dormitory::query()->whereHas('school', fn (Builder $query): Builder => $query->where('organization_id', $this->organization->id));
    }
}
