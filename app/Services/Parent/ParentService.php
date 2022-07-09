<?php

namespace App\Services\Parent;

use App\Models\User;
use App\Services\User\UserService;

class ParentService
{
    /**
     * User service variable.
     *
     * @var \App\Services\User\UserService
     */
    public UserService $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all parents in school.
     *
     * @return Illuminate\Eloquent\Database\Collection|static[]
     */
    public function getAllParents()
    {
        return $this->user->getUsersByRole('parent')->load('parentRecord');
    }

    /**
     * Create a new parent.
     *
     * @param collection $record
     *
     * @return void
     */
    public function createParent($record)
    {
        $parent = $this->user->createUser($record);
        $parent->assignRole('parent');
        $parent->parentRecord()->create();

        session()->flash('success', 'parent Created Successfully');
    }

    /**
     * Update a parent.
     *
     * @param User                    $parent
     * @param array|object|collection $records
     *
     * @return void
     */
    public function updateParent(User $parent, $records)
    {
        $this->user->updateUser($parent, $records, 'parent');

        return session()->flash('success', 'Parent Updated Successfully');
    }

    /**
     * Delete parent record.
     *
     * @param User $parent
     *
     * @return void
     */
    public function deleteParent(User $parent)
    {
        $this->user->deleteUser($parent);

        return session()->flash('success', 'Parent Deleted Successfully');
    }

    /**
     * Print a uset profiel.
     *
     * @param string $name
     * @param string $view
     * @param array  $data
     *
     * @return mixed
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($name, $view, $data);
    }

    /**
     * Add student as child of parent or remove student from parent.
     *
     * @param App\Models\Users $parent
     * @param int              $student
     * @param bool             $assign
     *
     * @return void
     */
    public function assignStudentToParent(User $parent, int $student, bool $assign = true)
    {
        $student = $this->user->getUserById($student);
        if (!$this->user->verifyRole($student->id, 'student')) {
            session()->flash('danger', 'User is not a student');

            return;
        }
        if ($assign == false) {
            $parent->parentRecord->students()->detach($student);
            session()->flash('success', 'Student successfully removed from parent');
        } else {
            $parent->parentRecord->students()->syncWithoutDetaching($student);
            session()->flash('success', 'Student successfully assigned to parent');
        }
    }
}
