<?php

namespace App\Listeners;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;

/**
 * Keep every role and permission change in the audit log.
 *
 * Access changes are the most sensitive writes in the application, so each one
 * records who changed it, whose access changed, and which names moved.
 */
class RecordPermissionChanges
{
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(RoleAttachedEvent::class, [self::class, 'handleRoleAttached']);
        $events->listen(RoleDetachedEvent::class, [self::class, 'handleRoleDetached']);
        $events->listen(PermissionAttachedEvent::class, [self::class, 'handlePermissionAttached']);
        $events->listen(PermissionDetachedEvent::class, [self::class, 'handlePermissionDetached']);
    }

    /**
     * Record a role given to a user.
     */
    public function handleRoleAttached(RoleAttachedEvent $event): void
    {
        $this->record(AuditAction::RoleAttached, $event->model, 'roles', $this->names($event->rolesOrIds, app(RoleContract::class)));
    }

    /**
     * Record a role taken from a user.
     */
    public function handleRoleDetached(RoleDetachedEvent $event): void
    {
        $this->record(AuditAction::RoleDetached, $event->model, 'roles', $this->names($event->rolesOrIds, app(RoleContract::class)));
    }

    /**
     * Record a permission given to a user or a role.
     */
    public function handlePermissionAttached(PermissionAttachedEvent $event): void
    {
        $this->record(AuditAction::PermissionAttached, $event->model, 'permissions', $this->names($event->permissionsOrIds, app(PermissionContract::class)));
    }

    /**
     * Record a permission taken from a user or a role.
     */
    public function handlePermissionDetached(PermissionDetachedEvent $event): void
    {
        $this->record(AuditAction::PermissionDetached, $event->model, 'permissions', $this->names($event->permissionsOrIds, app(PermissionContract::class)));
    }

    /**
     * Write one access change to the log.
     *
     * @param array<int, string> $names
     */
    private function record(AuditAction $action, Model $subject, string $key, array $names): void
    {
        if ($names === []) {
            return;
        }

        $this->auditor->record($action, $subject, [$key => $names]);
    }

    /**
     * Turn the event payload into plain names.
     *
     * The payload can hold names, keys, enums, models, or a collection of any
     * of those, so each shape is resolved to the name people recognise.
     *
     * @param Model $lookup the role or permission model used to resolve keys
     *
     * @return array<int, string>
     */
    private function names(mixed $value, Model $lookup): array
    {
        $items = $value instanceof \Traversable ? iterator_to_array($value) : Arr::wrap($value);

        $names = [];
        $keys = [];

        foreach ($items as $item) {
            if ($item instanceof Model) {
                $names[] = (string) $item->getAttribute('name');
            } elseif ($item instanceof BackedEnum) {
                $names[] = (string) $item->value;
            } elseif (is_int($item) || (is_string($item) && ctype_digit($item))) {
                $keys[] = $item;
            } elseif (is_string($item)) {
                $names[] = $item;
            }
        }

        if ($keys !== []) {
            $names = array_merge($names, $lookup->newQuery()->whereKey($keys)->pluck('name')->all());
        }

        return array_values(array_unique($names));
    }
}
