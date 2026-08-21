<?php

namespace App\Services\Subject;

use App\Actions\Curriculum\AssignTeacher;
use App\Exceptions\ResourceNotEmptyException;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    /**
     * Instance of user class.
     */
    public UserService $user;

    public function __construct(UserService $user, private AssignTeacher $assignTeacher)
    {
        $this->user = $user;
    }

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
     * @param  mixed  $data
     * @return void
     */
    public function createSubject($data)
    {
        $subject = Subject::firstOrCreate(['name' => $data['name']], [
            'short_name' => $data['short_name'],
            'school_id' => current_school_id(),
            'my_class_id' => $data['my_class_id'],
        ]);

        if (!$subject->wasRecentlyCreated) {
            throw new ResourceNotEmptyException('Subject already exists or something went wrong');
        }

        if (isset($data['teachers'])) {
            $teachers = [];
            foreach ($data['teachers'] as $teacher) {
                if ($this->user->verifyRole($teacher, 'teacher')) {
                    $teachers[] = $teacher;
                }
            }

            $this->syncTeachers($subject, $teachers);
        }
    }

    /**
     * Update subject.
     *
     * @param  mixed  $data
     * @return void
     */
    public function updateSubject(Subject $subject, $data)
    {
        $subject->name = $data['name'];
        $subject->short_name = $data['short_name'];

        $subject->save();

        if (isset($data['teachers'])) {
            $teachers = [];
            foreach ($data['teachers'] as $teacher) {
                if ($this->user->getUserById($teacher)->exists() && $this->user->verifyRole($teacher, 'teacher')) {
                    $teacher = intval($teacher);
                    $teachers[] = $teacher;
                }
            }
            $this->syncTeachers($subject, $teachers);
        } else {
            $this->syncTeachers($subject, []);
        }
    }

    /**
     * Make the teaching assignments of a subject match the given list.
     *
     * A teacher who is added gets an assignment for the working period. A
     * teacher who is removed keeps the assignment, which is given an end date,
     * so last year's records still say who taught.
     *
     * @param  array<int, int|string>  $teacherIds
     */
    public function syncTeachers(Subject $subject, array $teacherIds): void
    {
        $teacherIds = array_map('intval', array_filter(array_values($teacherIds)));

        foreach ($teacherIds as $teacherId) {
            $teacher = $this->user->getUserById($teacherId);

            if ($teacher === null) {
                continue;
            }

            $this->assignTeacher->assign($subject, $teacher, actor: auth()->user());
        }

        $running = TeachingAssignment::query()
            ->where('subject_id', $subject->id)
            ->runningOn()
            ->get();

        foreach ($running as $assignment) {
            if (!in_array($assignment->user_id, $teacherIds, true)) {
                $this->assignTeacher->end($assignment, actor: auth()->user());
            }
        }
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

    /**
     * Assign a teacher to a list of subjects.
     *
     * @param  array|mixed  $records  Array or collection of ids
     * @return void
     */
    public function assignTeacherToSubjects(User $teacher, $records)
    {
        $subjectIds = array_map('intval', array_filter(array_values($records['subjects'] ?? [])));

        foreach ($subjectIds as $subjectId) {
            $subject = Subject::inSchool()->find($subjectId);

            if ($subject === null) {
                continue;
            }

            $this->assignTeacher->assign($subject, $teacher, actor: auth()->user());
        }

        $running = TeachingAssignment::inSchool()
            ->forTeacher($teacher)
            ->runningOn()
            ->get();

        foreach ($running as $assignment) {
            if (!in_array($assignment->subject_id, $subjectIds, true)) {
                $this->assignTeacher->end($assignment, actor: auth()->user());
            }
        }
    }
}
