<?php

namespace App\Livewire\Concerns;

use App\Models\School;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Hold the details a campus is known by, for the create and edit forms.
 *
 * @mixin Component
 */
trait EditsSchoolDetails
{
    public string $name = '';

    public string $initials = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $country = '';

    public string $state = '';

    public string $city = '';

    public string $postalCode = '';

    /** @var TemporaryUploadedFile|null */
    public $logo = null;

    protected function fillSchoolDetails(School $school): void
    {
        $this->name = $school->name;
        $this->initials = (string) $school->initials;
        $this->phone = (string) $school->phone;
        $this->email = (string) $school->email;
        $this->address = (string) $school->address;
        $this->country = (string) $school->country;
        $this->state = (string) $school->state;
        $this->city = (string) $school->city;
        $this->postalCode = (string) $school->postal_code;
    }

    /**
     * @return array<string, list<string>>
     */
    protected function schoolDetailRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'initials' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'min:5', 'max:255', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:8', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'postalCode' => ['required', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function schoolDetailAttributes(): array
    {
        return ['postalCode' => 'postal code', 'initials' => 'short name'];
    }

    /**
     * @return array{name: string, initials: string|null, phone: string|null, email: string|null, address: string, country: string, state: string, city: string, postal_code: string, logo?: TemporaryUploadedFile}
     */
    protected function schoolDetails(): array
    {
        $blankToNull = static fn (string $value): ?string => trim($value) === '' ? null : trim($value);

        return [
            'name' => trim($this->name),
            'initials' => $blankToNull($this->initials),
            'phone' => $blankToNull($this->phone),
            'email' => $blankToNull($this->email),
            'address' => trim($this->address),
            'country' => $this->country,
            'state' => $this->state,
            'city' => trim($this->city),
            'postal_code' => trim($this->postalCode),
            ...($this->logo === null ? [] : ['logo' => $this->logo]),
        ];
    }
}
