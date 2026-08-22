<?php

namespace App\Services\Subject;

use App\Exceptions\ResourceNotEmptyException;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    /**
     * Get all subjects.
     *
     * @return Collection
     */
    public function getAllSubjects()
    {
        return Subject::inSchool()->get();
    }

    /**
     * Get a subject by Id.
     *
     *
     * @return Subject
     */
    public function getSubjectById(int $id)
    {
        return Subject::find($id);
    }

    /**
     * Create subject.
     *
     * @param array{name: string, short_name: string} $data
     */
    public function createSubject(array $data): void
    {
        $subject = Subject::firstOrCreate(['school_id' => current_school_id(), 'name' => $data['name']], [
            'short_name' => $data['short_name'],
        ]);

        if (!$subject->wasRecentlyCreated) {
            throw new ResourceNotEmptyException('Subject already exists or something went wrong');
        }
    }

    /**
     * Update subject.
     *
     * @param array{name: string, short_name: string} $data
     */
    public function updateSubject(Subject $subject, array $data): void
    {
        $subject->name = $data['name'];
        $subject->short_name = $data['short_name'];

        $subject->save();
    }

    /**
     * Delete subject.
     *
     *
     * @return void
     */
    public function deleteSubject(Subject $subject)
    {
        $subject->timetableRecord()->delete();
        $subject->delete();
    }
}
