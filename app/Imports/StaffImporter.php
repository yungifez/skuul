<?php

namespace App\Imports;

use App\Actions\Identity\ProvisionAccount;
use App\Contracts\Importer;
use App\Enums\EmploymentType;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Bring members of staff in from a file.
 *
 * The row makes the account and the employment record together. It does not
 * give anybody a teaching assignment: who teaches what stays a decision the
 * curriculum makes.
 */
class StaffImporter implements Importer
{
    public function __construct(private ProvisionAccount $provisionAccount) {}

    /**
     * Get the name people choose the import with.
     */
    public function key(): string
    {
        return 'staff';
    }

    /**
     * Get the title to show in the interface.
     */
    public function title(): string
    {
        return 'Staff';
    }

    /**
     * Get the columns a file must have.
     *
     * @return array<int, string>
     */
    public function requiredColumns(): array
    {
        return ['name', 'email', 'birthday', 'gender'];
    }

    /**
     * Get the columns a file may have.
     *
     * @return array<int, string>
     */
    public function optionalColumns(): array
    {
        return [
            'source_id',
            'staff_number',
            'job_title',
            'department',
            'employment_type',
            'joined_on',
            'address',
            'city',
            'state',
            'nationality',
            'blood_group',
            'phone',
        ];
    }

    /**
     * Get the rules one row must follow.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:511'],
            'email' => ['required', 'email:rfc', 'max:511'],
            'birthday' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'string', 'max:255'],
            'staff_number' => ['nullable', 'string', 'max:30'],
            'employment_type' => ['nullable', Rule::in(EmploymentType::values())],
            'joined_on' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Write one checked row.
     *
     * @param  array<string, mixed>  $row
     */
    public function apply(array $row, ?Model $existing): Model
    {
        $person = $existing instanceof StaffProfile
            ? $existing->user
            : $this->accountFor($row);

        $profile = StaffProfile::firstOrNew([
            'school_id' => current_school_id(),
            'user_id' => $person->id,
        ]);

        $profile->fill([
            'staff_number' => $row['staff_number'] ?? $profile->staff_number,
            'job_title' => $row['job_title'] ?? $profile->job_title,
            'department' => $row['department'] ?? $profile->department,
            'employment_type' => EmploymentType::tryFrom((string) ($row['employment_type'] ?? '')) ?? $profile->employment_type,
            'joined_on' => isset($row['joined_on']) ? Carbon::parse($row['joined_on']) : $profile->joined_on,
        ]);

        $profile->save();

        return $profile;
    }

    /**
     * Make or find the account this row belongs to.
     *
     * @param  array<string, mixed>  $row
     */
    private function accountFor(array $row): User
    {
        return $this->provisionAccount->provision([
            'name' => $row['name'],
            'email' => $row['email'],
            'school_id' => current_school_id(),
            'birthday' => $row['birthday'],
            'gender' => $row['gender'],
            'address' => $row['address'] ?? 'Not given',
            'blood_group' => $row['blood_group'] ?? 'Not given',
            'nationality' => $row['nationality'] ?? 'Not given',
            'state' => $row['state'] ?? 'Not given',
            'city' => $row['city'] ?? 'Not given',
            'phone' => $row['phone'] ?? null,
        ]);
    }
}
