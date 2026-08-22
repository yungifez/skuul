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
    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    public function publish(Syllabus $syllabus, ?User $actor = null): Syllabus
    {
        return DB::transaction(function () use ($syllabus, $actor): Syllabus {
            $syllabus = Syllabus::query()->lockForUpdate()->findOrFail($syllabus->id);

            if ($syllabus->status !== SyllabusStatus::Draft) {
                throw new InvalidValueException('Only a draft syllabus can be published.');
            }

            if ($syllabus->revision_of_id !== null) {
                Syllabus::query()->lockForUpdate()->findOrFail($syllabus->revision_of_id)->update(['status' => SyllabusStatus::Superseded]);
            }

            $syllabus->update(['status' => SyllabusStatus::Published, 'published_at' => now(), 'published_by' => $actor?->id]);
            $this->auditor->record(AuditAction::SyllabusPublished, $syllabus, ['revision' => $syllabus->revision], $actor);

            return $syllabus;
        });
    }
}
