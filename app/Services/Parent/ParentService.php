<?php

namespace App\Services\Parent;

use App\Models\User;
use App\Services\User\UserService;

class ParentService 
{
    
    /**
     * User service variable
     *
     * @var \App\Services\User\UserService
     */
    public UserService $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all parents in school
     *
     * @return Illuminate\Eloquent\Database\Collection|static[]
     */
    public function getAllParents()
    {
        return $this->user->getUsersByRole('parent')->load('parentRecord');
    }

    /**
     * Create a new parent
     *
     * @param collection $record
     * 
     * @return void
     */
    public function createParent($record)
    {
        $parent = $this->user->createUser($record);
        $parent->assignRole('parent');
        session()->flash('success', 'parent Created Successfully');

        return;
    }

    /**
     * Update a parent
     *
     * @param User $parent
     * @param array|object|collection $records
     * @return void
     */
    public function updateParent(User $parent, $records)
    {
        $this->user->updateUser($parent, $records, 'parent');

        return session()->flash('success', 'Parent Updated Successfully');
    }

    /**
     * Delete parent record
     *
     * @param User $parent
     * @return void
     */
    public function deleteParent(User $parent)
    {
        $this->user->deleteUser($parent);

        return session()->flash('success', 'Parent Deleted Successfully');
    }

    /**
     * Print a uset profiel
     *
     * @param string $name
     * @param string $view
     * @param array $data
     * 
     * @return mixed
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($name, $view, $data);
    }
}
