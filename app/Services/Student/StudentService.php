<?php

namespace App\Services\Student;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
use App\Enums\AcademicStructureStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\Role;
use App\Exceptions\EmptyRecordsException;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicCycleSection;
use App\Models\Promotion;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\Print\PrintService;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentService
{
    /**
     * Instance of user service.
     *
     * @var UserService
     */
    public $userService;

    /**
     * Instance of the enrollment state action.
     */
    public ChangeEnrollmentStatus $changeEnrollmentStatusAction;

    /**
     * Instance of the enrollment placement action.
     */
    public ChangeEnrollmentPlacement $changeEnrollmentPlacementAction;

    public function __construct(
        UserService $userService,
        ChangeEnrollmentStatus $changeEnrollmentStatusAction,
        ChangeEnrollmentPlacement $changeEnrollmentPlacementAction,
    ) {
        $this->userService = $userService;
        $this->changeEnrollmentStatusAction = $changeEnrollmentStatusAction;
        $this->changeEnrollmentPlacementAction = $changeEnrollmentPlacementAction;
    }

    /**
     * Get all students in school.
     *
     * @return Collection<int, User>
     */
    public function getAllStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord');
    }

    /**
     * Get all active students in school.
     *
     * @return Collection<int, User>
     */
    public function getAllActiveStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function (User $student) {
            return $student->studentRecord?->status === EnrollmentStatus::Active;
        });
    }

    /**
     * Get all graduated students in school.
     *
     * @return Collection<int, User>
     */
    public function getAllGraduatedStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function (User $student) {
            return $student->studentRecord?->isGraduated() === true;
        });
    }

    /**
     * Get a student by id.
     *
     * @param  array<int, int>|int  $id  student id
     * @return User|Collection<int, User>|null
     */
    public function getStudentById($id)
    {
        return $this->userService->getUserById($id)->load('studentRecord');
    }

    /**
     * Create student.
     */
    public function createStudent(array $record): User
    {
        $this->userService->failIfAlreadyHolds($record['email'], Role::Student);

        $person = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($record['email'])])->first();

        if ($person?->worksAsStaff() === true) {
            throw ValidationException::withMessages([
                'email' => "{$person->name} works as staff. A member of staff cannot be admitted as a learner.",
            ]);
        }

        $enrolledElsewhere = StudentRecord::query()
            ->whereRelation('user', 'email', $record['email'])
            ->where('school_id', '!=', current_school_id())
            ->enrolled()
            ->exists();

        if ($enrolledElsewhere) {
            throw ValidationException::withMessages([
                'email' => 'This learner is enrolled at another school. Ask that school to move or transfer them.',
            ]);
        }

        return DB::transaction(function () use ($record): User {
            $student = $this->userService->createUser($record);
            $student->assignRole(Role::Student);

            $this->createStudentRecord($student, $record);

            return $student;
        });
    }

    /**
     * Create record for student.
     *
     * @param  array<string, mixed>  $record
     *
     * @throws InvalidValueException
     */
    public function createStudentRecord(User $student, array $record): void
    {
        $record['admission_number'] ??= $this->generateAdmissionNumber();

        if (current_academic_year_id() == null) {
            throw new EmptyRecordsException('Academic Year not set');
        }

        $academicCycleSection = AcademicCycleSection::inSchool()
            ->whereKey($record['academic_cycle_section_id'])
            ->where('academic_year_id', current_academic_year_id())
            ->where('status', AcademicStructureStatus::Active)
            ->firstOrFail();

        $enrollment = StudentRecord::firstOrCreate([
            'user_id' => $student->id,
            'school_id' => current_school_id(),
        ], [
            'admission_number' => $record['admission_number'],
            'admission_date' => $record['admission_date'],
        ]);

        // The first placement starts the student's placement history.
        $this->changeEnrollmentPlacementAction->place(
            enrollment: $enrollment,
            academicCycleSection: $academicCycleSection,
            actor: auth()->user(),
            reason: 'Admission',
        );
    }

    /**
     * Update student.
     *
     *
     * @return void
     */
    public function updateStudent(User $student, $records)
    {
        $student = $this->userService->updateUser($student, $records);
    }

    /**
     * Delete student.
     *
     * The learner leaves this school first. An enrollment left active would
     * keep their seat, their place on registers, and the bills of a learner
     * nobody can open any more.
     */
    public function deleteStudent(User $student): void
    {
        DB::transaction(function () use ($student): void {
            $enrollment = StudentRecord::query()
                ->inSchool()
                ->where('user_id', $student->id)
                ->enrolled()
                ->first();

            if ($enrollment !== null) {
                $this->changeEnrollmentStatusAction->change(
                    enrollment: $enrollment,
                    status: EnrollmentStatus::Withdrawn,
                    actor: auth()->user(),
                    reason: 'Removed from the school',
                );
            }

            $this->userService->deleteUser($student);
        });
    }

    /**
     * Generate admission number.
     *
     * @return string
     */
    public function generateAdmissionNumber($schoolId = null)
    {
        $schoolInitials = (School::find($schoolId) ?? current_school())->initials;
        $schoolInitials != null && $schoolInitials .= '/';
        $currentYear = date('y');
        do {
            $admissionNumber = "$schoolInitials"."$currentYear/".\mt_rand('100000', '999999');
            if (StudentRecord::where('admission_number', $admissionNumber)->count() <= 0) {
                $uniqueAdmissionNumberFound = true;
            } else {
                $uniqueAdmissionNumberFound = false;
            }
        } while ($uniqueAdmissionNumberFound == false);

        return $admissionNumber;
    }

    /**
     * Print student profile.
     *
     *
     * @return Response
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::page($view, $data);
    }

    /**
     * Move the chosen learners from one section to another and keep a record of the move.
     *
     * The source section is locked, so a second click on a page left open finds the
     * learners already moved and writes nothing.
     *
     * @param  array{source_academic_cycle_section_id: int, destination_academic_cycle_section_id: int, student_id: array<int, int>}  $records
     *
     * @throws EmptyRecordsException
     * @throws InvalidValueException
     */
    public function promoteStudents(array $records): Promotion
    {
        return DB::transaction(function () use ($records): Promotion {
            $source = AcademicCycleSection::inSchool()->whereKey($records['source_academic_cycle_section_id'])->lockForUpdate()->firstOrFail();
            $destination = AcademicCycleSection::inSchool()->findOrFail($records['destination_academic_cycle_section_id']);

            if ($source->is($destination)) {
                throw new InvalidValueException('Choose a destination other than the current section.');
            }

            $students = $this->getAllActiveStudents()
                ->whereIn('id', $records['student_id'])
                ->filter(fn (User $student): bool => $student->studentRecord?->academic_cycle_section_id === $source->id);

            if ($students->isEmpty()) {
                throw new EmptyRecordsException('None of the chosen learners are still in this section. They may have been moved already.', 1);
            }

            foreach ($students as $student) {
                $this->changeEnrollmentPlacementAction->place(
                    enrollment: $student->studentRecord,
                    academicCycleSection: $destination,
                    actor: auth()->user(),
                    reason: 'Promotion',
                );
            }

            return Promotion::create([
                'source_academic_cycle_section_id' => $source->id,
                'destination_academic_cycle_section_id' => $destination->id,
                'students' => $students->pluck('id')->values(),
                'academic_year_id' => $destination->academic_year_id,
                'school_id' => current_school_id(),
            ]);
        });
    }

    /**
     * Get all promotions.
     *
     * @return Collection
     */
    public function getAllPromotions()
    {
        return Promotion::inSchool()->get();
    }

    /**
     * Get promotions by academic year Id.
     *
     * @param  int  $academicYearId  The Primary key of the academic year
     * @return Collection
     */
    public function getPromotionsByAcademicYearId(int $academicYearId)
    {
        return Promotion::inSchool()->where('academic_year_id', $academicYearId)->get();
    }

    /**
     * Put the learners of a promotion back in their old section.
     *
     * Only learners still sitting in the section they were promoted to go
     * back. A learner who moved on since, graduated, or left stays where they
     * are. Every learner goes back, or none does.
     */
    public function resetPromotion(Promotion $promotion): void
    {
        DB::transaction(function () use ($promotion): void {
            $students = $this->getStudentById($promotion->students);
            $sourceAcademicCycleSection = AcademicCycleSection::inSchool()
                ->findOrFail($promotion->source_academic_cycle_section_id);

            foreach ($students as $student) {
                $enrollment = $student->allStudentRecords;

                if ($enrollment === null
                    || $enrollment->status->isClosed()
                    || $enrollment->academic_cycle_section_id !== $promotion->destination_academic_cycle_section_id) {
                    continue;
                }

                $this->changeEnrollmentPlacementAction->place(
                    enrollment: $enrollment,
                    academicCycleSection: $sourceAcademicCycleSection,
                    actor: auth()->user(),
                    reason: 'Promotion reset',
                );
            }

            $promotion->delete();
        });
    }

    /**
     * Graduate the chosen learners of one section.
     *
     * The section is locked and every learner graduates or none does. Learners
     * who left the section or already graduated are skipped.
     *
     * @param  array{academic_cycle_section_id: int, student_id: array<int, int>, reason?: string|null}  $records
     * @return int The number of learners who graduated.
     *
     * @throws InvalidValueException
     */
    public function graduateStudents(array $records): int
    {
        return DB::transaction(function () use ($records): int {
            $section = AcademicCycleSection::inSchool()->whereKey($records['academic_cycle_section_id'])->lockForUpdate()->firstOrFail();

            $students = $this->getAllActiveStudents()
                ->whereIn('id', $records['student_id'])
                ->filter(fn (User $student): bool => $student->studentRecord?->academic_cycle_section_id === $section->id);

            if ($students->isEmpty()) {
                throw new InvalidValueException('None of the chosen learners are still active in this section. They may have graduated already.');
            }

            foreach ($students as $student) {
                $this->changeEnrollmentStatusAction->graduate(
                    $student->studentRecord,
                    auth()->user(),
                    $records['reason'] ?? null,
                );
            }

            return $students->count();
        });
    }

    /**
     * Reset Graduation.
     *
     *
     * @return void
     */
    public function resetGraduation(User $student, ?string $reason = null)
    {
        $enrollment = $student->graduatedStudentRecord;

        if ($enrollment === null) {
            return;
        }

        $this->changeEnrollmentStatusAction->returnToAttendance($enrollment, auth()->user(), $reason);
    }
}
