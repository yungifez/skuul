<?php

namespace App\Actions\Cohort;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\InvalidValueException;
use App\Models\Program;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Open a programme, rename it, or close it.
 */
class SaveProgram
{
    public function __construct(private RecordAuditEvent $audit) {}

    /**
     * Open a programme in the working school.
     *
     * @param  array{name: string, type: string, description: string|null}  $attributes
     *
     * @throws InvalidValueException when the school already has a programme with that name
     */
    public function create(array $attributes, User $actor): Program
    {
        try {
            return DB::transaction(function () use ($attributes, $actor): Program {
                School::query()->lockForUpdate()->findOrFail(current_school_id());
                $this->refuseATakenName($attributes['name']);

                $program = Program::create([
                    ...$attributes,
                    'school_id' => current_school_id(),
                ]);

                $this->audit->record(AuditAction::ProgramChanged, $program, ['created' => true], $actor);

                return $program;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has a programme with that name.');
        }
    }

    /**
     * Rename a programme, describe it again, or close it.
     *
     * @param  array{name: string, description: string|null, is_active: bool}  $attributes
     *
     * @throws InvalidValueException when another group already has that name
     */
    public function update(Program $program, array $attributes, User $actor): Program
    {
        try {
            return DB::transaction(function () use ($program, $attributes, $actor): Program {
                School::query()->lockForUpdate()->findOrFail($program->school_id);
                $program = Program::query()->lockForUpdate()->findOrFail($program->getKey());
                $this->refuseATakenName($attributes['name'], $program);

                $program->fill($attributes);

                if (!$program->isDirty()) {
                    return $program;
                }

                $changed = array_keys($program->getDirty());
                $program->save();
                $this->audit->record(AuditAction::ProgramChanged, $program, ['changed' => $changed], $actor);

                return $program;
            });
        } catch (UniqueConstraintViolationException) {
            throw new InvalidValueException('This school already has a programme with that name.');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function refuseATakenName(string $name, ?Program $ignore = null): void
    {
        $isTaken = Program::query()
            ->inSchool()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($ignore !== null, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($isTaken) {
            throw new InvalidValueException('This school already has a programme with that name.');
        }
    }
}
