<?php

namespace App\Actions\Curriculum;

use App\Exceptions\InvalidValueException;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\InstructionalModelException;
use Illuminate\Support\Facades\DB;

class DeleteAcademicLevel
{
    /**
     * Delete a reusable level that has not been used by setup data.
     *
     * @throws InvalidValueException when the level is still referenced
     */
    public function delete(AcademicLevel $academicLevel): void
    {
        DB::transaction(function () use ($academicLevel): void {
            /** @var AcademicLevel $academicLevel */
            $academicLevel = AcademicLevel::query()
                ->lockForUpdate()
                ->findOrFail($academicLevel->id);

            if ($academicLevel->children()->exists()) {
                throw new InvalidValueException('Move the child levels first before deleting this level group.');
            }

            if ($academicLevel->cycleSections()->exists()) {
                throw new InvalidValueException('This level has year sections. Keep it for history instead of deleting it.');
            }

            if (CourseOffering::query()->where('academic_level_id', $academicLevel->id)->exists()) {
                throw new InvalidValueException('This level has subjects attached to it. Keep it for history instead of deleting it.');
            }

            if (InstructionalModelException::query()->where('academic_level_id', $academicLevel->id)->exists()) {
                throw new InvalidValueException('This level has teaching setup attached to it. Keep it for history instead of deleting it.');
            }

            $academicLevel->delete();
        });
    }
}
