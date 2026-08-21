<?php

namespace App\Actions\Wellbeing;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Models\StudentHealthRecord;
use App\Models\StudentRecord;
use App\Models\User;

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

    public function __construct(private RecordAuditEvent $auditor)
    {
    }

    /**
     * Write the health record of one child.
     *
     * @param array<string, mixed> $information
     */
    public function record(StudentRecord $enrollment, array $information, ?User $actor = null): StudentHealthRecord
    {
        $values = array_intersect_key($information, array_flip(self::FIELDS));

        $record = StudentHealthRecord::firstOrNew(['student_record_id' => $enrollment->id]);

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
            ['student_record_id' => $enrollment->id, 'fields' => array_values(array_diff($changed, ['updated_by']))],
            $actor,
        );

        return $record;
    }
}
