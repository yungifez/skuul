<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * The identity and contact details every person form asks for.
 *
 * The using component must also use Livewire's WithFileUploads.
 */
trait EditsPersonDetails
{
    /** @var array<int, string> */
    private const GENDERS = ['Male', 'Female', 'Non-binary', 'Prefer not to say'];

    public string $name = '';

    public string $email = '';

    /**
     * The address the person already has, so a save that keeps it does not
     * look it up again.
     */
    #[Locked]
    public string $storedEmail = '';

    public string $birthday = '';

    public string $gender = '';

    public string $phone = '';

    public string $address = '';

    public string $addressLine2 = '';

    public string $nationality = '';

    public string $country = '';

    public string $state = '';

    public string $city = '';

    public string $postalCode = '';

    /** @var TemporaryUploadedFile|UploadedFile|null */
    public $profilePhoto = null;

    /**
     * The country picker is its own component and reports each choice.
     */
    #[On('country-updated')]
    public function syncCountry(?string $country = null): void
    {
        $this->country = $country ?? '';
    }

    #[On('state-updated')]
    public function syncState(?string $state = null): void
    {
        $this->state = $state ?? '';
    }

    protected function fillPersonDetails(User $user): void
    {
        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->storedEmail = $this->email;
        $this->birthday = $user->birthday ? substr((string) $user->birthday, 0, 10) : '';
        $this->gender = $this->knownGender((string) $user->gender);
        $this->phone = (string) $user->phone;
        $this->address = (string) $user->address;
        $this->addressLine2 = (string) $user->address_line_2;
        $this->nationality = (string) $user->nationality;
        $this->country = (string) $user->country;
        $this->state = (string) $user->state;
        $this->city = (string) $user->city;
        $this->postalCode = (string) $user->postal_code;
    }

    /**
     * Match a stored gender to a choice of the form, whatever its case. An unknown value shows as not specified.
     */
    private function knownGender(string $gender): string
    {
        foreach (self::GENDERS as $choice) {
            if (strcasecmp($choice, trim($gender)) === 0) {
                return $choice;
            }
        }

        return '';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function personDetailRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', $this->email === $this->storedEmail ? 'email:rfc' : new_email_rule(), 'max:100'],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before:today'],
            'gender' => ['nullable', 'string', Rule::in(self::GENDERS)],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postalCode' => ['nullable', 'string', 'max:30'],
            'profilePhoto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function personDetailAttributes(): array
    {
        return [
            'addressLine2' => 'address line 2',
            'postalCode' => 'postal code',
            'profilePhoto' => 'profile picture',
        ];
    }

    /**
     * Trim the typed details before they are checked.
     */
    protected function trimPersonDetails(): void
    {
        foreach (['name', 'email', 'phone', 'address', 'addressLine2', 'nationality', 'city', 'postalCode'] as $property) {
            $this->{$property} = trim($this->{$property});
        }
    }

    /**
     * The details in the shape the user services read. Blank values are null.
     *
     * @return array<string, mixed>
     */
    protected function personDetails(): array
    {
        $blankToNull = fn (string $value): ?string => $value === '' ? null : $value;

        return [
            'name' => $this->name,
            'email' => $this->email,
            'birthday' => $blankToNull($this->birthday),
            'gender' => $blankToNull($this->gender),
            'phone' => $blankToNull($this->phone),
            'address' => $blankToNull($this->address),
            'address_line_2' => $blankToNull($this->addressLine2),
            'nationality' => $blankToNull($this->nationality),
            'country' => $blankToNull($this->country),
            'state' => $blankToNull($this->state),
            'city' => $blankToNull($this->city),
            'postal_code' => $blankToNull($this->postalCode),
            'profile_photo' => $this->profilePhoto,
        ];
    }

    /**
     * Show a refusal from a service under the field it names on this form.
     */
    protected function showPersonDetailErrors(ValidationException $exception): void
    {
        $fields = [
            'photo' => 'profilePhoto',
            'profile_photo' => 'profilePhoto',
            'address_line_2' => 'addressLine2',
            'postal_code' => 'postalCode',
        ];

        foreach ($exception->errors() as $key => $messages) {
            foreach ($messages as $message) {
                $this->addError($fields[$key] ?? $key, $message);
            }
        }
    }
}
