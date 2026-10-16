<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Who may read the calendar and put days on it.
 *
 * Reading and publishing are different permissions. A member of staff can
 * draft a closure day; saying the school is shut is a decision somebody else
 * makes.
 */
class CalendarEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can see the calendar.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read calendar event') && !$this->isPortalOnly($user);
    }

    /**
     * Determine whether the user can read one event.
     *
     * A draft is readable only by the people who can work on it, because it
     * is not yet something the school has said.
     */
    public function view(User $user, CalendarEvent $event): bool
    {
        if ($this->isPortalOnly($user)) {
            return false;
        }

        if ($event->school_id !== current_school_id()) {
            return false;
        }

        if (!$event->is_published) {
            return $user->can('update calendar event') || $event->created_by === $user->id;
        }

        return $user->can('read calendar event');
    }

    /**
     * Determine whether the user can add a day to the calendar.
     */
    public function create(User $user): bool
    {
        return $user->can('create calendar event');
    }

    /**
     * Determine whether the user can change an event.
     */
    public function update(User $user, CalendarEvent $event): bool
    {
        return $user->can('update calendar event') && $event->school_id === current_school_id();
    }

    /**
     * Determine whether the user can put the event in front of the school.
     */
    public function publish(User $user, CalendarEvent $event): bool
    {
        return $user->can('publish calendar event') && $event->school_id === current_school_id();
    }

    /**
     * Determine whether the user can take the event off the calendar.
     */
    public function delete(User $user, CalendarEvent $event): bool
    {
        return $user->can('delete calendar event') && $event->school_id === current_school_id();
    }

    /**
     * Portal roles read calendar entries from a child-scoped portal screen.
     */
    private function isPortalOnly(User $user): bool
    {
        $roles = collect($user->getRoleNames());

        return $roles->intersect(['parent', 'student'])->isNotEmpty()
            && $roles->diff(['parent', 'student'])->isEmpty();
    }
}
