<?php

namespace App\Actions\Curriculum;

use App\Actions\Audit\RecordAuditEvent;
use App\Enums\AuditAction;
use App\Enums\RosterMode;
use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\InstructionalModelException;
use App\Models\Subject;
use App\Models\User;

/**
 * Allow one subject to be taught outside the campus model, in writing.
 *
 * The campus model does not move. One subject is let out of it for one cycle,
 * with a reason anybody can read later.
 */
class GrantOfferingException
{
    public function __construct(private RecordAuditEvent $auditor) {}

    /**
     * Allow the exception.
     *
     * @throws InvalidValueException when the records do not fit or the model already allows it
     */
    public function grant(
        AcademicYear $academicYear,
        Subject $subject,
        RosterMode $rosterMode,
        string $reason,
        ?AcademicLevel $academicLevel = null,
        ?User $actor = null,
    ): InstructionalModelException {
        if (trim($reason) === '') {
            throw new InvalidValueException('Say why this subject is taught differently.');
        }

        if ($subject->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('That subject belongs to another campus.');
        }

        if ($academicLevel !== null && $academicLevel->school_id !== $academicYear->school_id) {
            throw new InvalidValueException('That level belongs to another campus.');
        }

        if (instructional_model($academicYear)->allowsRosterMode($rosterMode)) {
            throw new InvalidValueException('The campus model already allows this, so no exception is needed.');
        }

        if ($academicYear->isClosed()) {
            throw new InvalidValueException('This cycle is closed.');
        }

        $existing = InstructionalModelException::query()
            ->running()
            ->where('academic_year_id', $academicYear->id)
            ->where('subject_id', $subject->id)
            ->where('roster_mode', $rosterMode)
            ->where('academic_level_id', $academicLevel?->id)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $exception = InstructionalModelException::create([
            'school_id' => $academicYear->school_id,
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'academic_level_id' => $academicLevel?->id,
            'roster_mode' => $rosterMode,
            'reason' => $reason,
            'granted_by' => $actor === null ? auth()->id() : $actor->id,
        ]);

        $this->auditor->record(
            AuditAction::OfferingExceptionGranted,
            $exception,
            [
                'subject' => $subject->name,
                'roster_mode' => $rosterMode->value,
                'level' => $academicLevel?->name,
                'reason' => $reason,
            ],
            $actor,
            $academicYear->school_id,
        );

        return $exception;
    }

    /**
     * Take the exception back.
     *
     * Offerings already created under it are left alone. Withdrawing the
     * permission does not unteach a class that has been running for a term.
     *
     * @throws InvalidValueException when it was already taken back
     */
    public function revoke(InstructionalModelException $exception, ?User $actor = null): InstructionalModelException
    {
        if (!$exception->isRunning()) {
            throw new InvalidValueException('This exception was already taken back.');
        }

        $exception->revoked_at = now();
        $exception->revoked_by = $actor === null ? auth()->id() : $actor->id;
        $exception->save();

        $this->auditor->record(
            AuditAction::OfferingExceptionRevoked,
            $exception,
            ['subject_id' => $exception->subject_id],
            $actor,
            $exception->school_id,
        );

        return $exception;
    }
}
