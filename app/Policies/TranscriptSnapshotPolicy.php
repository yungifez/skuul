<?php

namespace App\Policies;

use App\Models\TranscriptSnapshot;
use App\Models\User;

class TranscriptSnapshotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return $user->can('read report') && $transcriptSnapshot->school_id === current_school_id();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create report');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }
}
