<?php

namespace App\Actions\Syllabus;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\Syllabus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublishSyllabus
{
    public function __construct(private RecordAuditEvent $auditor) {}

    public function publish(Syllabus $syllabus, ?User $actor = null): Syllabus
    {
        return DB::transaction(function () use ($syllabus, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Draft) {
                throw new InvalidValueException('Only a draft syllabus can be published.');
            }

            if (!$syllabus->topics()->exists()) {
                throw new InvalidValueException('Add at least one topic before publishing the syllabus.');
            }

            if ($syllabus->revision_of_id !== null) {
                $replaced = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->revision_of_id);

                if ($replaced->status !== SyllabusStatus::Published) {
                    throw new InvalidValueException('The syllabus this draft revises is no longer the published one. Delete this draft and revise the current syllabus.');
                }

                $replaced->update(['status' => SyllabusStatus::Superseded]);
            }

            $syllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now(), 'published_by' => $actor?->id]);
            $this->auditor->record(AuditAction::SyllabusPublished, $syllabus, ['revision' => $syllabus->revision], $actor);

            return $syllabus;
        });
    }
}
