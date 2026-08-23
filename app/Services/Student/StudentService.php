<?php

namespace App\Services\Student;

use App\Actions\Enrollment\ChangeEnrollmentPlacement;
use App\Actions\Enrollment\ChangeEnrollmentStatus;
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
     * @param array<int, int>|int $id student id
     *
     * @return User|Collection<int, User>|null
     */
    public function getStudentById($id)
    {
        return $this->userService->getUserById($id)->load('studentRecord');
    }

    /**
     * Create student.
     */
    public function createStudent(array $record): void
    {
        DB::transaction(function () use ($record) {
            $student = $this->userService->createUser($record);
            $student->assignRole(Role::Student);

            $this->createStudentRecord($student, $record);
        });
    }

    /**
     * Create record for student.
     *
     * @param array<string, mixed> $record
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
            ->firstOrFail();

        $enrollment = StudentRecord::firstOrCreate([
            'user_id'   => $student->id,
            'school_id' => current_school_id(),
        ], [
            'admission_number' => $record['admission_number'],
            'admission_date'   => $record['admission_date'],
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
     *
     * @return void
     */
    public function deleteStudent(User $student)
    {
        $student->delete();
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
        return PrintService::download($view, $data, $name);
    }

    /**
     * Promote students.
     *
     * @param array<mixed> $records
     *
     * @return void
     */
    public function promoteStudents($records)
    {
        $source = AcademicCycleSection::inSchool()->findOrFail($records['source_academic_cycle_section_id']);
        $destination = AcademicCycleSection::inSchool()->findOrFail($records['destination_academic_cycle_section_id']);

        $students = $this->getAllActiveStudents()
            ->whereIn('id', $records['student_id'])
            ->filter(fn (User $student): bool => $student->studentRecord?->academic_cycle_section_id === $source->id);

        // make sure there are students to promote
        if (!$students->count()) {
            throw new EmptyRecordsException('No students to promote', 1);
        }

        foreach ($students as $student) {
            $this->changeEnrollmentPlacementAction->place(
                enrollment: $student->studentRecord,
                academicCycleSection: $destination,
                actor: auth()->user(),
                reason: 'Promotion',
            );
        }

        Promotion::create([
            'source_academic_cycle_section_id'      => $source->id,
            'destination_academic_cycle_section_id' => $destination->id,
            'students'                              => $students->pluck('id'),
            'academic_year_id'                      => $destination->academic_year_id,
            'school_id'                             => current_school_id(),
        ]);
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
     * @param int $academicYearId The Primary key of the academic year
     *
     * @return Collection
     */
    public function getPromotionsByAcademicYearId(int $academicYearId)
    {
        return Promotion::inSchool()->where('academic_year_id', $academicYearId)->get();
    }

    /**
     * Reset promotion.
     *
     * @param Promotion $promotion instance of promotion to reset
     *
     * @return void
     */
    public function resetPromotion(Promotion $promotion)
    {
        $students = $this->getStudentById($promotion->students);
        $sourceAcademicCycleSection = AcademicCycleSection::inSchool()
            ->findOrFail($promotion->source_academic_cycle_section_id);

        foreach ($students as $student) {
            // A person listed in an old promotion may no longer hold an
            // enrollment in this school. Leave them out.
            if ($student->allStudentRecords === null) {
                continue;
            }

            $this->changeEnrollmentPlacementAction->place(
                enrollment: $student->allStudentRecords,
                academicCycleSection: $sourceAcademicCycleSection,
                actor: auth()->user(),
                reason: 'Promotion reset',
            );
        }

        $promotion->delete();
    }

    /**
     * Graduate students.
     *
     * @param mixed $records
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function graduateStudents($records)
    {
        // get all students for graduation
        $students = $this->getAllActiveStudents()->whereIn('id', $records['student_id']);

        // make sure there are students to graduate
        if (!$students->count()) {
            throw new InvalidValueException('No students to graduate');
        }

        // record the graduation of each student, with its reason and actor
        foreach ($students as $student) {
            $this->changeEnrollmentStatusAction->graduate(
                $student->studentRecord,
                auth()->user(),
                $records['reason'] ?? null,
            );
        }
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
