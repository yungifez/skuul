<?php

namespace App\Services\Subject;

use App\Models\Subject;
use App\Services\User\UserService;

class SubjectService
{
    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    public function getAllSubjects()
    {
        return Subject::where(['school_id' => auth()->user()->school_id])->get();
    }

    //get subject by id

    public function getSubjectById($id)
    {
        return Subject::find($id);
    }

    //create subject

    public function createSubject($data)
    {
        $subject = Subject::firstOrCreate([
            'name'        => $data['name'],
            'short_name'  => $data['short_name'],
            'school_id'   => auth()->user()->school_id,
            'my_class_id' => $data['my_class_id'],
        ]);

        if (!$subject->wasRecentlyCreated) {
            return session()->flash('danger', 'Subject already exists or something went wrong');
        }

        if (isset($data['teachers'])) {
            $teachers = [];
            foreach ($data['teachers'] as $teacher) {
                if ($this->user->verifyRole($teacher, 'teacher')) {
                    $teachers[] = $teacher;
                }
            }

            $subject->teachers()->sync($teachers);
        }

        return session()->flash('success', 'Subject created successfully');
    }

    //update subject

    public function updateSubject(object $subject, $data)
    {
        $subject->name = $data['name'];
        $subject->short_name = $data['short_name'];

        $subject->save();

        if (isset($data['teachers'])) {
            $teachers = [];
            foreach ($data['teachers'] as $teacher) {
                if ($this->user->verifyRole($teacher, 'teacher')) {
                    $teachers[] = $teacher;
                }
            }
            $subject->teachers()->sync($teachers);
        } else {
            $subject->teachers()->sync([]);
        }

        return session()->flash('success', 'Subject updated successfully');
    }

    //delete subject

    public function deleteSubject(Subject $subject)
    {
        $subject->delete();

        return session()->flash('success', 'Subject deleted successfully');
    }
}
