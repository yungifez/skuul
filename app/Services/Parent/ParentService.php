<?php

namespace App\Services\Parent;

use App\Actions\Identity\ChangeGuardianLink;
use App\Enums\Role;
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

    public function __construct(UserService $user, private ChangeGuardianLink $changeGuardianLink)
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
     * @param  array|Collection  $record
     * @return User
     */
    public function createParent($record)
    {
        $this->user->failIfAlreadyHolds($record['email'], Role::Parent);

        $parent = DB::transaction(function () use ($record) {
            $parent = $this->user->createUser($record);
            $parent->assignRole(Role::Parent);
            // One guardian record per person, shared by every school they join.
            $parent->parentRecord()->firstOrCreate(['user_id' => $parent->id]);

            return $parent;
        });

        return $parent;
    }

    /**
     * Update a parent.
     *
     * @param  array|object|Collection  $records
     * @return User
     */
    public function updateParent(User $parent, $records)
    {
        $parent = $this->user->updateUser($parent, $records, 'parent');

        return $parent;
    }

    /**
     * Delete a parent, or only remove them from the working school.
     *
     * The portal follows guardian links, not memberships. A parent who stays
     * at another school would still read this school's children, so those
     * links end first.
     */
    public function deleteParent(User $parent): void
    {
        DB::transaction(function () use ($parent): void {
            $learners = $parent->parentRecord?->students()
                ->whereHas('studentRecords', fn ($enrollments) => $enrollments->inSchool())
                ->get() ?? collect();

            foreach ($learners as $learner) {
                $this->changeGuardianLink->unlink($parent, $learner, auth()->user());
            }

            $this->user->deleteUser($parent);
        });
    }

    /**
     * Print a user profile.
     *
     *
     * @return mixed
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::page($view, $data);
    }
}
