<?php

namespace App\Imports;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Identity\ProvisionAccount;
use App\Contracts\Importer;
use App\Enums\Role;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Student\StudentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Bring students in from a file.
 *
 * The file names the academic level and the cycle section by name, because the
 * people who keep these files do not know the numbers this application uses. A
 * row that names a student who is already here moves them; it never makes a
 * second enrollment.
 */
class StudentImporter implements Importer
{
    public function __construct(
        private ProvisionAccount $provisionAccount,
        private ChangeEnrollmentPlacement $changePlacement,
        private StudentService $students,
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
        return ['name', 'email', 'birthday', 'level', 'section'];
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
            'gender',
            'admission_number',
            'admission_date',
            'address',
            'city',
            'state',
            'nationality',
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'birthday' => ['required', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'max:100'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'admission_number' => ['nullable', 'string', 'max:255'],
            'admission_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Write one checked row.
     *
     * @param  array<string, mixed>  $row
     *
     * @throws InvalidValueException when the level or section is not in this school and cycle
     */
    public function apply(array $row, ?Model $existing): Model
    {
        $academicLevel = AcademicLevel::inSchool()
            ->where('name', $row['level'])
            ->first();

        if ($academicLevel === null) {
            throw new InvalidValueException("There is no level called {$row['level']}.");
        }

        $academicCycleSection = AcademicCycleSection::inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->where('academic_level_id', $academicLevel->id)
            ->where('name', $row['section'])
            ->first();

        if ($academicCycleSection === null) {
            throw new InvalidValueException("The level {$row['level']} has no section called {$row['section']} in this cycle.");
        }

        $this->refuseALearnerWhoAttendsElsewhere($existing instanceof StudentRecord ? $existing->user?->email : $row['email']);

        $student = $this->accountFor($row, $existing);

        if (!$student->hasRole(Role::Student->value)) {
            $student->assignRole(Role::Student);
        }

        $enrollment = StudentRecord::query()
            ->where('user_id', $student->id)
            ->where('school_id', current_school_id())
            ->first()
            ?? StudentRecord::create([
                'user_id' => $student->id,
                'school_id' => current_school_id(),
                'admission_number' => $this->freeAdmissionNumber($row['admission_number'] ?? null),
                'admission_date' => Carbon::parse($row['admission_date'] ?? now()),
            ]);

        $this->changePlacement->place(
            enrollment: $enrollment,
            academicCycleSection: $academicCycleSection,
            reason: 'Imported',
        );

        return $enrollment->refresh();
    }

    /**
     * Refuse a row for a learner who is enrolled and attending at another school.
     *
     * The admission form refuses the same person. A second enrollment would
     * bypass the campus move and the transfer, which carry the history.
     *
     * @throws InvalidValueException
     */
    private function refuseALearnerWhoAttendsElsewhere(?string $email): void
    {
        $elsewhere = StudentRecord::query()
            ->whereRelation('user', 'email', $email)
            ->where('school_id', '!=', current_school_id())
            ->enrolled()
            ->with('school:id,name')
            ->first();

        if ($elsewhere !== null) {
            throw new InvalidValueException("This learner attends {$elsewhere->school?->name}. Ask that school to move or transfer them.");
        }

        $person = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower((string) $email)])->first();

        if ($person?->worksAsStaff() === true) {
            throw new InvalidValueException("{$person->name} works as staff. A member of staff cannot be admitted as a learner.");
        }
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
            'gender' => $row['gender'] ?? null,
            'address' => $row['address'] ?? 'Not given',
            'nationality' => $row['nationality'] ?? null,
            'state' => $row['state'] ?? null,
            'city' => $row['city'] ?? null,
            'phone' => $row['phone'] ?? null,
        ]);
    }

    /**
     * Get the admission number for a new learner.
     *
     * A blank number is made the way the admission form makes it. A number
     * the school already uses is refused in words the reader can act on.
     *
     * @throws InvalidValueException when the school already uses the number
     */
    private function freeAdmissionNumber(?string $admissionNumber): string
    {
        if (blank($admissionNumber)) {
            return $this->students->generateAdmissionNumber(current_school_id());
        }

        if (StudentRecord::inSchool()->where('admission_number', $admissionNumber)->exists()) {
            throw new InvalidValueException("Admission number {$admissionNumber} is already used in this school.");
        }

        return $admissionNumber;
    }
}
