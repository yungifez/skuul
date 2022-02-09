<?php

namespace App\Services\Teacher;

use App\Models\User;
use App\Services\MyClass\MyClassService;
use App\Services\User\UserService;

class TeacherService
{
    public $user;

    public function __construct(UserService $user)
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

        return session()->flash('success', 'Teacher Created Successfully');
    }
}
