<?php

namespace App\Services\Student;

use App\Exceptions\EmptyRecordsException;
use App\Exceptions\InvalidValueException;
use App\Models\Promotion;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\MyClass\MyClassService;
use App\Services\Print\PrintService;
use App\Services\Section\SectionService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /**
     *Instance of class service.
     *
     * @var MyClassService
     */
    public $myClassService;

    /**
     * Instance of user service.
     *
     * @var UserService
     */
    public $userService;

    /**
     * Instance of section service.
     */
    public SectionService $sectionService;

    public function __construct(MyClassService $myClassService, UserService $userService, SectionService $sectionService)
    {
        $this->myClassService = $myClassService;
        $this->sectionService = $sectionService;
        $this->userService = $userService;
    }

    /**
     * Get all students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord');
    }

    /**
     * Get all active students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function ($student) {
            if ($student->studentRecord) {
                return $student->studentRecord->is_graduated == false;
            }
        });
    }

    /**
     * Get all graduated students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGraduatedStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function ($student) {
            return $student->studentRecord()->withoutGlobalScopes()->first()->is_graduated == true;
        });
    }

    /**
     * Get a student by id.
     *
     * @param array|int $id student id
     *
     * @return \App\Models\User
     */
    public function getStudentById($id)
    {
        return $this->userService->getUserById($id)->load('studentRecord');
    }

    /**
     * Create student.
     *
     * @param array $record Array of student record
     *
     * @return void
     */
    public function createStudent($record)
    {
        DB::transaction(function () use ($record) {
            $student = $this->userService->createUser($record);
            $student->assignRole('student');

            $this->createStudentRecord($student, $record);
        });
    }

    /**
     * Create record for student.
     *
     * @param User         $student $name
     * @param array|object $record
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function createStudentRecord(User $student, $record)
    {
        $record['admission_number'] || $record['admission_number'] = $this->generateAdmissionNumber();
        $section = $this->sectionService->getSectionById($record['section_id']);
        if (!$this->myClassService->getClassById($record['my_class_id'])->sections->contains($section)) {
            throw new InvalidValueException('Section is not in class');
        }

        if (auth()->user()->school->academic_year_id == null) {
            throw new EmptyRecordsException('Academic Year not set');
        }

        $student->studentRecord()->firstOrCreate([
            'user_id' => $student->id,
        ], [
            'my_class_id'      => $record['my_class_id'],
            'section_id'       => $record['section_id'],
            'admission_number' => $record['admission_number'],
            'admission_date'   => $record['admission_date'],
        ]);

        //create record history
        $currentAcademicYear = $student->school->academicYear;
        $student->studentRecord->load('academicYears')->academicYears()->sync([$currentAcademicYear->id => [
            'my_class_id' => $record['my_class_id'],
            'section_id'  => $record['section_id'],
        ]]);
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
        $schoolInitials = (School::find($schoolId) ?? auth()->user()->school)->initials;
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
     * @return \Illuminate\Http\Response
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($view, $data)->download($name.'.pdf');
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
        $oldClass = $this->myClassService->getClassById($records['old_class_id']);
        $newClass = $this->myClassService->getClassById($records['new_class_id']);
        $academicYear = auth()->user()->school->academic_year_id;

        if (!$oldClass->sections()->where('id', $records['old_section_id'])->exists()) {
            throw new InvalidValueException('Old section is not in old class');
        }

        if (!$newClass->sections()->where('id', $records['new_section_id'])->exists()) {
            throw new InvalidValueException('New section is not in new class');
        }

        //make sure academic year is present
        if ($academicYear == null) {
            throw new InvalidValueException('Academic year is not set');
        }

        //get all students for promotion
        $students = $this->getAllActiveStudents()->whereIn('id', $records['student_id']);

        // make sure there are students to promote
        if (!$students->count()) {
            throw new EmptyRecordsException('No students to promote', 1);
        }

        $currentAcademicYear = auth()->user()->school->academicYear;
        // update each student's class
        foreach ($students as $student) {
            if (in_array($student->id, $records['student_id'])) {
                $student->studentRecord()->update([
                    'my_class_id' => $records['new_class_id'],
                    'section_id'  => $records['new_section_id'],
                ]);
                $student->studentRecord->load('academicYears')->academicYears()->syncWithoutDetaching([$currentAcademicYear->id => [
                    'my_class_id' => $records['new_class_id'],
                    'section_id'  => $records['new_section_id'],
                ]]);
            }
        }

        // create promotion record
        Promotion::create([
            'old_class_id'     => $records['old_class_id'],
            'new_class_id'     => $records['new_class_id'],
            'old_section_id'   => $records['old_section_id'],
            'new_section_id'   => $records['new_section_id'],
            'students'         => $students->pluck('id'),
            'academic_year_id' => $academicYear,
            'school_id'        => auth()->user()->school_id,
        ]);
    }

    /**
     * Get all promotions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPromotions()
    {
        return Promotion::where('school_id', auth()->user()->school_id)->get();
    }

    /**
     * Get promotions by academic year Id.
     *
     * @param int $academicYearId The Primary key of the academic year
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPromotionsByAcademicYearId(int $academicYearId)
    {
        return Promotion::where('school_id', auth()->user()->school_id)->where('academic_year_id', $academicYearId)->get();
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
        $currentAcademicYear = auth()->user()->school->academicYear;

        foreach ($students as $student) {
            $student->allStudentRecords->load('academicYears')->academicYears()->syncWithoutDetaching([$currentAcademicYear->id => [
                'my_class_id' => $promotion->old_class_id,
                'section_id'  => $promotion->old_section_id,
            ]]);
            $student->allStudentRecords()->update([
                'my_class_id' => $promotion->old_class_id,
                'section_id'  => $promotion->old_section_id,
            ]);
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
        //get all students for graduation
        $students = $this->getAllActiveStudents()->whereIn('id', $records['student_id']);

        // make sure there are students to graduate
        if (!$students->count()) {
            throw new InvalidValueException('No students to graduate');
        }

        // update each student's graduation status
        foreach ($students as $student) {
            if (in_array($student->id, $records['student_id'])) {
                $student->studentRecord()->update([
                    'is_graduated' => true,
                ]);
            }
        }
    }

    /**
     * Reset Graduation.
     *
     *
     * @return void
     */
    public function resetGraduation(User $student)
    {
        $student->graduatedStudentRecord()->update([
            'is_graduated' => false,
        ]);
    }
}
