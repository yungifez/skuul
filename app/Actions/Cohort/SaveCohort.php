<?php

namespace App\Actions\Cohort;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Cohort;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Make a group, rename it, or close it.
 */
class SaveCohort
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Make a group in the working school.
     *
     * @param  array{name: string, type: string, description: string|null}  $attributes
     *
     * @throws InvalidValueException when the school already has a group with that name
     */
    public function create(array $attributes, User $actor): Cohort
    {
        try {
            return DB::transaction(function () use ($attributes, $actor): Cohort {
                School::query()->lockForUpdate()->findOrFail(current_school_id());
                $this->refuseATakenName($attributes['name']);

                $cohort = Cohort::create([
                    ...$attributes,
                    'school_id' => current_school_id(),
                    'academic_year_id' => current_academic_year_id(),
                    'created_by' => $actor->id,
                ]);

                $this->audit->record(AuditAction::CohortChanged, $cohort, ['created' => true], $actor);

                return $cohort;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has a group with that name.');
        }
    }

    /**
     * Rename a group, describe it again, or close it.
     *
     * @param  array{name: string, description: string|null, is_active: bool}  $attributes
     *
     * @throws InvalidValueException when another group already has that name
     */
    public function update(Cohort $cohort, array $attributes, User $actor): Cohort
    {
        try {
            return DB::transaction(function () use ($cohort, $attributes, $actor): Cohort {
                School::query()->lockForUpdate()->findOrFail($cohort->school_id);
                $cohort = Cohort::query()->lockForUpdate()->findOrFail($cohort->getKey());
                $this->refuseATakenName($attributes['name'], $cohort);

                $cohort->fill($attributes);

                if (!$cohort->isDirty()) {
                    return $cohort;
                }

                $changed = array_keys($cohort->getDirty());
                $cohort->save();
                $this->audit->record(AuditAction::CohortChanged, $cohort, ['changed' => $changed], $actor);

                return $cohort;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has a group with that name.');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function refuseATakenName(string $name, ?Cohort $ignore = null): void
    {
        $isTaken = Cohort::query()
            ->inSchool()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($ignore !== null, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException('This school already has a group with that name.');
        }
    }
}
