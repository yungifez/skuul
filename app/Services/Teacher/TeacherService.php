<?php

namespace App\Services\Teacher;

use App\Models\User;
use App\Services\User\UserService;
use App\Services\MyClass\MyClassService;


class TeacherService
{
    public $user;

    public function __construct( UserService $user)
    {
        $this->user = $user;
    }
  
    public function getAllTeachers()
    {
        return $this->user->getUsersByRole('teacher')->load('teacherRecord');
    }

    //create teacher method

    public function createTeacher($record)
    { 
        $student = $this->user->createUser($record);

        $student->assignRole('teacher');

        $student->teacherRecord()->create([
            'my_class_id' => $record['my_class_id'],
            'section_id' => $record['section_id'],
        ]);
    }
}