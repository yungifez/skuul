<?php

namespace App\Services\Parent;

use App\Exceptions\InvalidUserException;
use App\Models\User;
use App\Services\Print\PrintService;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ParentService
{
    /**
     * User service variable.
     */
    public UserService $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all parents in school.
     */
    public function getAllParents(): Collection|static
    {
        return $this->user->getUsersByRole('parent')->load('parentRecord');
    }

    /**
     * Create a new parent.
     *
     * @param array|collection $record
     *
     * @return User
     */
    public function createParent($record)
    {
        $parent = DB::transaction(function () use ($record) {
            $parent = $this->user->createUser($record);
            $parent->assignRole('parent');
            $parent->parentRecord()->create(['user_id' => $parent->id]);

            return $parent;
        });

        return $parent;
    }

    /**
     * Update a parent.
     *
     * @param array|object|collection $records
     *
     * @return User
     */
    public function updateParent(User $parent, $records)
    {
        $parent = $this->user->updateUser($parent, $records, 'parent');

        return $parent;
    }

    /**
     * Delete parent record.
     *
     *
     * @return void
     */
    public function deleteParent(User $parent)
    {
        $this->user->deleteUser($parent);
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

    /**
     * Add student as child of parent or remove student from parent.
     *
     * @param \App\Models\User $parent
     * @param int              $student
     * @param bool             $assign
     *
     * @throws InvalidUserException
     *
     * @return void
     */
    public function assignStudentToParent(User $parent, int $student, bool $assign = true)
    {
        $student = $this->user->getUserById($student);
        if (!$this->user->verifyRole($student->id, 'student')) {
            throw new InvalidUserException('User is not a student', 1);

            return;
        }

        if ($assign == false) {
            $parent->parentRecord->students()->detach($student);
        } else {
            $parent->parentRecord->students()->syncWithoutDetaching($student);
        }
    }
}
