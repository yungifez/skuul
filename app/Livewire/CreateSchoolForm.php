<?php

namespace App\Livewire;

use App\Actions\School\OpenCampus;
use App\Enums\PlatformPermission;
use App\Livewire\Concerns\EditsSchoolDetails;
use App\Models\Organization;
use App\Models\School;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Nnjeim\World\Models\Country;

/**
 * Add a campus to an organization the person may grow.
 */
class CreateSchoolForm extends Component
{
    use EditsSchoolDetails;
    use WithFileUploads;

    public string $organizationId = '';

    public function mount(): void
    {
        Gate::authorize('create', School::class);

        if ($this->organizations()->count() === 1) {
            $this->organizationId = (string) $this->organizations()->first()->id;
        }
    }

    /**
     * Only the organizations this person may add a campus to.
     *
     * @return Collection<int, Organization>
     */
    #[Computed]
    public function organizations(): Collection
    {
        $user = auth()->user();

        $organizations = $user->can(PlatformPermission::AccessAllOrganizations)
            ? Organization::query()->orderBy('name')->get()
            : $user->organizations()->orderBy('name')->get();

        return $organizations->filter(fn (Organization $organization): bool => $user->can('createForOrganization', [School::class, $organization]))->values();
    }

    public function save(OpenCampus $openCampus): void
    {
        Gate::authorize('create', School::class);

        $this->validate(
            ['organizationId' => ['required', 'integer'], ...$this->schoolDetailRules()],
            [],
            ['organizationId' => 'organization', ...$this->schoolDetailAttributes()],
        );

        $organization = $this->organizations()->firstWhere('id', (int) $this->organizationId);

        if ($organization === null) {
            $this->addError('organizationId', 'Choose an organization you can add a campus to.');

            return;
        }

        Gate::authorize('createForOrganization', [School::class, $organization]);

        $school = $openCampus->open($organization, $this->schoolDetails(), auth()->user());
        school_context()->set($school, remember: false);

        session()->flash('success', 'School created. Let’s set up the essentials.');
        $this->redirectRoute('schools.setup', [$school, 'details']);
    }

    public function render(): View
    {
        return view('livewire.create-school-form', [
            'countries' => Country::query()->orderBy('name')->get(['name']),
        ]);
    }
}
