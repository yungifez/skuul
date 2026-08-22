<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviseSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /** @param array{name?: string, description?: string|null, file?: string} $changes */
    public function revise(Syllabus $syllabus, array $changes = [], ?User $actor = null): Syllabus
    {
        return DB::transaction(function () use ($syllabus, $changes, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Published) {
                throw new InvalidValueException('Only a published syllabus can be revised.');
            }

            $revision = Syllabus::create([
                'name' => $changes['name'] ?? $syllabus->name,
                'description' => $changes['description'] ?? $syllabus->description,
                'file' => $changes['file'] ?? $syllabus->file,
                'course_offering_id' => $syllabus->course_offering_id,
                'status' => SyllabusStatus::Draft,
                'revision' => $syllabus->revision + 1,
                'revision_of_id' => $syllabus->id,
            ]);

            $this->auditor->record(AuditAction::SyllabusRevised, $revision, ['revision_of_id' => $syllabus->id, 'revision' => $revision->revision], $actor);

            return $revision;
        });
    }
}
