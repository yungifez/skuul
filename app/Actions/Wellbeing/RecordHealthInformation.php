<?php

namespace App\Actions\Wellbeing;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Exceptions\StaleRecordException;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Keep the health facts the school needs in an emergency.
 *
 * One child has one health record. Writing it again changes the record it
 * already has, and the audit log says who changed what.
 */
class RecordHealthInformation
{
    /**
     * The fields a school may keep.
     *
     * @var array<int, string>
     */
    private const FIELDS = [
        'blood_group',
        'conditions',
        'allergies',
        'medications',
        'dietary_needs',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'notes',
    ];

    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Write the health record of one child.
     *
     * A caller that read the record earlier passes the values it read. A
     * field somebody else changed since then is refused rather than written
     * over, because a first aider acts on what this record says.
     *
     * @param  array<string, mixed>  $information
     * @param  array<string, mixed>|null  $asRead
     *
     * @throws StaleRecordException when somebody else changed a field this save changes
     */
    public function record(StudentRecord $enrollment, array $information, ?User $actor = null, ?array $asRead = null): StudentHealthRecord
    {
        $values = array_map(
            fn (mixed $value): mixed => is_string($value) && trim($value) === '' ? null : $value,
            array_intersect_key($information, array_flip(self::FIELDS)),
        );

        return DB::transaction(function () use ($enrollment, $values, $actor, $asRead): StudentHealthRecord {
            // The learner's row is locked, so two first saves cannot both
            // create a record for the same child.
            StudentRecord::query()->lockForUpdate()->findOrFail($enrollment->getKey());

            $record = StudentHealthRecord::query()->firstOrNew(['student_record_id' => $enrollment->id]);

            if ($asRead !== null) {
                $stale = array_keys(array_filter(
                    $values,
                    fn (mixed $value, string $field): bool => $record->{$field} !== ($asRead[$field] ?? null) && $record->{$field} !== $value,
                    ARRAY_FILTER_USE_BOTH,
                ));

                if ($stale !== []) {
                    throw new StaleRecordException($stale);
                }
            }

            $record->school_id = $enrollment->school_id;
            $record->fill($values);
            $record->updated_by = $actor === null ? auth()->id() : $actor->id;

            $changed = array_keys($record->getDirty());
            $record->save();

            $this->auditor->record(
                AuditAction::HealthRecordUpdated,
                $record,
                // The values themselves stay out of the log. Only the field names
                // are kept, so the log never becomes a second copy of the record.
                ['student_record_id' => $enrollment->id, 'fields' => array_values(array_diff($changed, ['updated_by', 'school_id', 'student_record_id']))],
                $actor,
            );

            return $record;
        });
    }
}
