<?php

namespace App\Services\Student;

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
}