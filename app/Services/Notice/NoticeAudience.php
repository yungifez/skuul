<?php

namespace App\Services\Notice;

use App\Models\MyClass;
use App\Models\Notice;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Work out who a notice is for.
 *
 * The audience is stored as plain keys on the notice, so a new way of
 * targeting people is a new key rather than a new table.
 */
class NoticeAudience
{
    /**
     * Get the people the notice should reach.
     *
     * An empty audience means everyone who works or learns in the school.
     *
     * @return Collection<int, User>
     */
    public function resolve(Notice $notice): Collection
    {
        $audience = $notice->audience ?? [];
        $schoolId = $notice->school_id;

        $query = User::query()->ofSchool($schoolId);

        $userIds = $this->userIds($audience, $schoolId);

        if ($userIds !== null) {
            $query->whereIn('users.id', $userIds);
        }

        if (!empty($audience['roles'])) {
            $roles = (array) $audience['roles'];
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->whereIn('name', $roles));
        }

        return $query->get();
    }

    /**
     * Get the people named by class, section, or a hand-picked list.
     *
     * Returning null means the audience did not narrow the list at all.
     *
     * @param array<string, mixed> $audience
     *
     * @return array<int, int>|null
     */
    private function userIds(array $audience, ?int $schoolId): ?array
    {
        $ids = [];
        $narrowed = false;

        if (!empty($audience['user_ids'])) {
            $ids = array_merge($ids, array_map('intval', (array) $audience['user_ids']));
            $narrowed = true;
        }

        foreach ((array) ($audience['section_ids'] ?? []) as $sectionId) {
            $section = Section::find($sectionId);
            $ids = array_merge($ids, $section?->students()->pluck('id')->all() ?? []);
            $narrowed = true;
        }

        foreach ((array) ($audience['class_ids'] ?? []) as $classId) {
            $class = MyClass::find($classId);
            $ids = array_merge($ids, $class?->students()->pluck('id')->all() ?? []);
            $narrowed = true;
        }

        return $narrowed ? array_values(array_unique(array_map('intval', $ids))) : null;
    }
}
