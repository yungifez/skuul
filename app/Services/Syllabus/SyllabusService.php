<?php

namespace App\Services\Syllabus;

use App\Enums\SyllabusStatus;
use App\Exceptions\InvalidValueException;
use App\Models\CourseOffering;
use App\Models\Syllabus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyllabusService
{
    /**
     * Store a syllabus for one exact offering.
     *
     * @param  array{name: string, description?: string|null, file: UploadedFile, course_offering_id: int}  $data
     */
    public function createSyllabus(array $data): Syllabus
    {
        /** @var CourseOffering $courseOffering */
        $courseOffering = CourseOffering::inSchool()
            ->with('academicPeriod')
            ->findOrFail($data['course_offering_id']);

        if ($courseOffering->academicPeriod->isClosed()) {
            throw new InvalidValueException('Reopen the academic period before adding a syllabus.');
        }

        return DB::transaction(function () use ($courseOffering, $data): Syllabus {
            return Syllabus::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'file' => $data['file']->store('syllabus', 'public'),
                'course_offering_id' => $courseOffering->id,
            ]);
        });
    }

    public function deleteSyllabus(Syllabus $syllabus): void
    {
        if ($syllabus->status !== SyllabusStatus::Draft) {
            throw new InvalidValueException('Published syllabus revisions are immutable. Create a revised draft instead.');
        }

        Storage::disk('public')->delete($syllabus->file);
        $syllabus->delete();
    }
}
