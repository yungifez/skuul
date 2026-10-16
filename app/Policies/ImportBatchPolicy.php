<?php

namespace App\Policies;

use App\Models\ImportBatch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may load a file, read what it will do, and write it.
 *
 * Checking a file and writing it are different permissions, because a person
 * who may prepare an import is not always the person who may change the
 * school's records with it.
 */
class ImportBatchPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the list of imports.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read import');
    }

    /**
     * Determine whether the user can read one import.
     *
     * An import holds whole-school data, so it never leaves its school.
     */
    public function view(User $user, ImportBatch $batch): bool
    {
        return $user->can('read import') && $batch->school_id === current_school_id();
    }

    /**
     * Determine whether the user can load a file.
     */
    public function create(User $user): bool
    {
        return $user->can('create import');
    }

    /**
     * Determine whether the user can write the import.
     */
    public function apply(User $user, ImportBatch $batch): bool
    {
        return $user->can('apply import') && $batch->school_id === current_school_id();
    }
}
