<?php

namespace App\Livewire;

use App\Actions\Sharing\RequestDataSharing;
use App\Enums\DataCategory;
use App\Exceptions\InvalidValueException;
use App\Models\DataSharingRequest;
use App\Models\School;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Ask another school for a learner's records.
 *
 * The learner is named by admission number in one named school, never chosen
 * from a list. A school must not be able to read the roll of a school it has
 * no records from.
 */
class CreateDataSharingRequestForm extends Component
{
    /**
     * How many admission numbers one person may get wrong in an hour.
     */
    private const MissedLookupsPerHour = 10;

    public string $holdingSchoolId = '';

    public string $admissionNumber = '';

    /**
     * The ticked categories, as the browser sends them back.
     *
     * @var array<int, string>
     */
    public array $categories = [];

    public string $purpose = '';

    public string $expiresOn = '';

    public function mount(): void
    {
        Gate::authorize('create', DataSharingRequest::class);
    }

    public function save(RequestDataSharing $requestDataSharing): void
    {
        Gate::authorize('create', DataSharingRequest::class);

        $this->admissionNumber = trim($this->admissionNumber);
        $this->purpose = trim($this->purpose);

        $this->validate([
            'holdingSchoolId' => ['required', 'integer', Rule::exists((new School)->getTable(), 'id')],
            'admissionNumber' => ['required', 'string', 'max:50'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => [Rule::enum(DataCategory::class)],
            'purpose' => ['required', 'string', 'max:500'],
            'expiresOn' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'categories.required' => 'A request must name what it asks for.',
            'expiresOn.after_or_equal' => 'A request cannot end before it starts.',
        ], [
            'holdingSchoolId' => 'school',
            'admissionNumber' => 'admission number',
            'expiresOn' => 'end date',
        ]);

        $guessKey = 'data-sharing-lookup:'.auth()->id();

        if (RateLimiter::tooManyAttempts($guessKey, self::MissedLookupsPerHour)) {
            $minutes = (int) ceil(RateLimiter::availableIn($guessKey) / 60);
            $this->addError('admissionNumber', "Too many admission numbers were not found. Try again in {$minutes} minutes.");

            return;
        }

        $enrollment = StudentRecord::query()
            ->where('school_id', (int) $this->holdingSchoolId)
            ->where('admission_number', $this->admissionNumber)
            ->first();

        if ($enrollment === null) {
            // The message says nothing about which half was wrong, so a wrong
            // guess never tells one school who attends another. Counting the
            // misses stops a person from walking through another school's roll.
            RateLimiter::hit($guessKey, 3600);
            $this->addError('admissionNumber', 'That school holds no learner with that admission number.');

            return;
        }

        try {
            $sharingRequest = $requestDataSharing->request(
                enrollment: $enrollment,
                requestingSchool: current_school(),
                purpose: $this->purpose,
                categories: array_map(DataCategory::from(...), array_values($this->categories)),
                expiresOn: $this->expiresOn === '' ? null : $this->expiresOn,
                actor: auth()->user(),
            );
        } catch (InvalidValueException $exception) {
            $this->addError('holdingSchoolId', $exception->getMessage());

            return;
        }

        session()->flash('success', 'The request was sent to the school that holds the records.');
        $this->redirectRoute('data-sharing-requests.show', $sharingRequest);
    }

    public function render(): View
    {
        return view('livewire.create-data-sharing-request-form', [
            'schools' => School::query()->whereKeyNot(current_school_id())->orderBy('name')->get(['id', 'name']),
            'dataCategories' => DataCategory::cases(),
        ]);
    }
}
