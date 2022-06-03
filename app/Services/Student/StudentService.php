<?php

namespace App\Services\Student;

use App\Models\User;
use App\Models\Promotion;
use Illuminate\Support\Str;
use App\Services\User\UserService;
use App\Services\Print\PrintService;
use App\Services\MyClass\MyClassService;

class StudentService
{
    public $myClassService;

    public $user;

    public function __construct(MyClassService $myClass, UserService $user)
    {
        $this->myClass = $myClass;
        $this->user = $user;
    }
  
    //gets active students
    public function getAllStudents()
    {
        return $this->user->getUsersByRole('student')->load('studentRecord')->filter(function ($student)
        {
            return $student->studentRecord->is_graduated == false;
        });
    }

    //gets graduated students

    public function getAllGraduatedStudents()
    {
        return $this->user->getUsersByRole('student')->load('studentRecord')->filter(function ($student)
        {
            return $student->studentRecord->is_graduated == true;
        });
    }

    // get student by id

    public function getStudentById($id)
    {
        return $this->user->getUserById($id)->load('studentRecord');
    }

    //create student method

    public function createStudent($record)
    {
        $student = $this->user->createUser($record);

        $student->assignRole('student');

        $record['admission_number'] || $record['admission_number'] = $this->generateAdmissionNumber();

        if (! $this->myClass->getClassById($record['my_class_id'])->isSectionInClass($record['section_id'])) {
            throw new \Exception('Section is not in the class');
        }

        $student->studentRecord()->create([
            'my_class_id' => $record['my_class_id'],
            'section_id' => $record['section_id'],
            'admission_number' => $record['admission_number'],
            'admission_date' => $record['admission_date'],
        ]);

        return session()->flash('success', 'Student Created Successfully');
    }

    public function updateStudent(User $student, $records)
    {
        $student = $this->user->updateUser($student, $records);

        return session()->flash('success', 'Student Updated Successfully');
    }

    public function deleteStudent(User $student)
    {
        $student->delete();

        return session()->flash('success', 'Student Deleted Successfully');
    }
  
    public function generateAdmissionNumber()
    {
        return Str::random(10);
    }
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($name, $view, $data)->download();
    }

    //promote student method
    public function promoteStudents($records)
    {
        $oldClass = $this->myClass->getClassById($records['old_class_id']);
        $newClass = $this->myClass->getClassById($records['new_class_id']);
        $records['academic_year_id'] = auth()->user()->school->academic_year_id;


        //check if the section is in  class
        if (!$oldClass->isSectionInClass($records['old_section_id'])) {
            throw new \Exception('Old section is not in the class');
        }

        // make sure section is in class
        if (!$newClass->isSectionInClass($records['new_section_id'])) {
            throw new \Exception('New section is not in the class');
        }

        //make sure academic year is present
        if ($records['academic_year_id'] == null) {
            return session()->flash('danger', 'Academic year is not set');
        }

        //get all students for promotion
        $students = $this->getAllStudents()->whereIn('id', $records['student_id']);

        // make sure there are students to promote
        if (!$students->count()) {
            return session()->flash('danger', 'No students to promote');
        }

        // update each student's class
        foreach ($students as $student) {
            if (in_array($student->id, $records['student_id'])) {
                $student->studentRecord()->update([
                    'my_class_id' => $records['new_class_id'],
                    'section_id' => $records['new_section_id'],
                ]);
            }
        }

        // create promotion record
        Promotion::create([
            'old_class_id' => $records['old_class_id'],
            'new_class_id' => $records['new_class_id'],
            'old_section_id' => $records['old_section_id'],
            'new_section_id' => $records['new_section_id'],
            'students' => $students->pluck('id'),
            'academic_year_id' => $records['academic_year_id'],
            'school_id' => auth()->user()->school_id,
        ]);

        return session()->flash('success', 'Students Promoted Successfully');
    }

    //get all promotion record

    public function getAllPromotions()
    {
        return Promotion::where('school_id', auth()->user()->school_id)->get();
    }

    public function getPromotionsByAcademicYearId($academicYearId)
    {
        return Promotion::where('school_id', auth()->user()->school_id)->where('academic_year_id' , $academicYearId)->get();
    }

    // reset promotion method

    public function resetPromotion($promotion)
    {
        $students = $this->getStudentById($promotion->students);

        foreach ($students as $student) {
            $student->studentRecord()->update([
                'my_class_id' => $promotion->old_class_id,
                'section_id' => $promotion->old_section_id,
            ]);
        }

        $promotion->delete();

        return session()->flash('success', 'Promotion Reset Successfully');
    }

    //graduate student method
    public function graduateStudents($records)
    {
        //get all students for graduation
        $students = $this->getAllStudents()->whereIn('id', $records['student_id']);

        // make sure there are students to graduate
        if (!$students->count()) {
            return session()->flash('danger', 'No students to graduate');
        }

        // update each student's graduation status
        foreach ($students as $student) {
            if (in_array($student->id, $records['student_id'])) {
                $student->studentRecord()->update([
                    'is_graduated' => true
                ]);
            }
        }

        return session()->flash('success', 'Students graduated Successfully');
    }

    //reset graduation method

    public function resetGraduation($student)
    {
        $student = $this->getStudentById($student);

        $student->studentRecord()->update([
            'is_graduated' => false
        ]);

        return session()->flash('success', 'Graduation Reset Successfully');
    }
}
