<?php

namespace App\Imports;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Identity\ProvisionAccount;
use App\Contracts\Importer;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Bring students in from a file.
 *
 * The file names the class and the section by name, because the people who
 * keep these files do not know the numbers this application uses. A row that
 * names a student who is already here moves them; it never makes a second
 * enrollment.
 */
class StudentImporter implements Importer
{
    public function __construct(
        private ProvisionAccount $provisionAccount,
        private ChangeEnrollmentPlacement $changePlacement,
    ) {}

    /**
     * Get the name people choose the import with.
     */
    public function key(): string
    {
        return 'students';
    }

    /**
     * Get the title to show in the interface.
     */
    public function title(): string
    {
        return 'Students';
    }

    /**
     * Get the columns a file must have.
     *
     * @return array<int, string>
     */
    public function requiredColumns(): array
    {
        return ['name', 'email', 'birthday', 'gender', 'class', 'section'];
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
            'admission_number',
            'admission_date',
            'address',
            'city',
            'state',
            'nationality',
            'blood_group',
            'religion',
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
            'class' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'admission_number' => ['nullable', 'string', 'max:255'],
            'admission_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Write one checked row.
     *
     * @param  array<string, mixed>  $row
     *
     * @throws InvalidValueException when the class or section is not in this school
     */
    public function apply(array $row, ?Model $existing): Model
    {
        // A class belongs to a school through its class group.
        $class = MyClass::query()
            ->whereHas('classGroup', fn ($query) => $query->where('school_id', current_school_id()))
            ->where('name', $row['class'])
            ->first();

        if ($class === null) {
            throw new InvalidValueException("There is no class called {$row['class']}.");
        }

        $section = $class->sections()->where('name', $row['section'])->first();

        if ($section === null) {
            throw new InvalidValueException("The class {$row['class']} has no section called {$row['section']}.");
        }

        $student = $this->accountFor($row, $existing);

        if (!$student->hasRole(Role::Student->value)) {
            $student->assignRole(Role::Student);
        }

        $enrollment = StudentRecord::firstOrCreate(
            ['user_id' => $student->id, 'school_id' => current_school_id()],
            [
                'my_class_id' => $class->id,
                'section_id' => $section->id,
                'admission_number' => $row['admission_number'] ?? null,
                'admission_date' => Carbon::parse($row['admission_date'] ?? now()),
            ],
        );

        $this->changePlacement->place(
            enrollment: $enrollment,
            class: $class,
            section: $section,
            reason: 'Imported',
        );

        return $enrollment->refresh();
    }

    /**
     * Get the account this row belongs to, making it when it is new.
     *
     * @param  array<string, mixed>  $row
     */
    private function accountFor(array $row, ?Model $existing): User
    {
        if ($existing instanceof StudentRecord) {
            return $existing->user;
        }

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
            'religion' => $row['religion'] ?? null,
            'phone' => $row['phone'] ?? null,
        ]);
    }
}
