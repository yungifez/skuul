<?php

namespace App\Services\Teacher;

use App\Models\User;
use App\Services\Print\PrintService;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;

class TeacherService
{
    /**
     * User service variable.
     */
    public userService $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all teachers in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTeachers()
    {
        return $this->user->getUsersByRole('teacher')->load('teacherRecord');
    }

    /**
     * Create a new teacher.
     *
     * @param Collection|array $record
     *
     * @return void
     */
    public function createTeacher($record)
    {
        $teacher = $this->user->createUser($record);
        $teacher->assignRole('teacher');
    }

    /**
     * Update a teacher.
     *
     * @param array|object|collection $records
     *
     * @return void
     */
    public function updateTeacher(User $teacher, $records)
    {
        $this->user->updateUser($teacher, $records, 'teacher');
    }

    /**
     * Delete teacher.
     *
     *
     * @return void
     */
    public function deleteTeacher(User $teacher)
    {
        $this->user->deleteUser($teacher);
    }

    /**
     * Print a user profile.
     *
     *
     * @return mixed
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($view, $data);
    }
}
