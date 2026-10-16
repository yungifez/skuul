<?php

namespace App\Policies;

use App\Models\TranscriptSnapshot;
use App\Models\User;

class TranscriptSnapshotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('read report');
    }

    public function view(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return $user->can('read report') && $transcriptSnapshot->school_id === current_school_id();
    }

    public function create(User $user): bool
    {
        return $user->can('create report');
    }

    public function update(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    public function delete(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    public function restore(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }

    public function forceDelete(User $user, TranscriptSnapshot $transcriptSnapshot): bool
    {
        return false;
    }
}
