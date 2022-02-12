<?php

namespace App\Services\Student;

use App\Models\Promotion;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\User\UserService;
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
  
    public function getAllStudents()
    {
        return $this->user->getUsersByRole('student')->load('studentRecord');
    }

    //create student method

    public function createStudent($record)
    { 
        $student = $this->user->createUser($record);

        $student->assignRole('student');

        $record['admission_number'] || $record['admission_number'] = $this->generateAdmissionNumber();

        if (!$this->myClass->getClassById($record['my_class_id'])->isSectionInClass($record['section_id'])) {
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

    public function generateAdmissionNumber()
    {
        return Str::random(10);
    }

    public function updateStudent($student, $records)
    {
        $student = $this->user->updateUser($student, $records);

        return session()->flash('success', 'Student Updated Successfully');
    }

    public function createPdfFromView(string $name,string $view,array $data)
    {
        $pdf = Pdf::loadView($view, $data);

        return $pdf->download("$name.pdf");
    }

    //promote student method
    public function promoteStudent($records)
    {
        $oldClass = $this->myClass->getClassById($records['old_class']);
        $newClass = $this->myClass->getClassById($records['new_class']);
        $records['academic_year'] = auth()->user()->school->academic_year_id;


        if (!$oldClass->isSectionInClass($records['old_section'])) {
            throw new \Exception('Old section is not in the class');
        }

        if (!$newClass->isSectionInClass($records['new_section'])) {
            throw new \Exception('New section is not in the class');
        }

        if ($records['academic_year'] == null) {
            return session()->flash('danger', 'Academic year is not set');
        }

        $students = $this->getAllStudents()->whereIn('id', $records['student_id']);

        foreach ($students as $student) {
            if (in_array($student->id, $records['student_id'])) {
                $student->studentRecord()->update([
                    'my_class_id' => $records['new_class'],
                    'section_id' => $records['new_section'],
                ]);

                Promotion::create([
                    'old_class_id' => $records['old_class'],
                    'new_class_id' => $records['new_class'],
                    'old_section_id' => $records['old_section'],
                    'new_section_id' => $records['new_section'],
                    'student_id' => $student->id,
                    'academic_year_id' => $records['academic_year'],
                ]);
            }
        }

        return session()->flash('success', 'Students Promoted Successfully');
    }
}