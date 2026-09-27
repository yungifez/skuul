<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Syllabus;
use App\Models\SyllabusTopic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviseSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Open a draft revision of a published syllabus, carrying its topics over.
     *
     * @param  array{name?: string, description?: string|null, file?: string|null, change_note?: string|null}  $changes
     */
    public function revise(Syllabus $syllabus, array $changes = [], ?User $actor = null): Syllabus
    {
        return DB::transaction(function () use ($syllabus, $changes, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Published) {
                throw new InvalidValueException('Only a published syllabus can be revised.');
            }

            if ($syllabus->openRevision() !== null) {
                throw new InvalidValueException('A draft revision of this syllabus is already open. Finish or delete it first.');
            }

            $revision = Syllabus::create([
                'name' => $changes['name'] ?? $syllabus->name,
                'description' => array_key_exists('description', $changes) ? $changes['description'] : $syllabus->description,
                'file' => array_key_exists('file', $changes) ? $changes['file'] : $syllabus->file,
                'change_note' => $changes['change_note'] ?? null,
                'course_offering_id' => $syllabus->course_offering_id,
                'status' => SyllabusStatus::Draft,
                'revision' => $syllabus->revision + 1,
                'revision_of_id' => $syllabus->id,
            ]);

            $revision->topics()->createMany($syllabus->topics->map(fn (SyllabusTopic $topic): array => [
                'copied_from_id' => $topic->id,
                ...$topic->only(['week', 'position', 'title', 'objectives', 'content', 'resources']),
            ])->all());

            $this->auditor->record(AuditAction::SyllabusRevised, $revision, ['revision_of_id' => $syllabus->id, 'revision' => $revision->revision, 'change_note' => $revision->change_note], $actor);

            return $revision;
        });
    }
}
