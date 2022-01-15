<?php
namespace App\Services\Subject;

use App\Models\Subject;

class SubjectService  
{
    public function getAllSubjects()
    {
        return Subject::where(['school_id' => auth()->user()->school_id])->get();
    }

   //create subject 

    public function createSubject($data)
    {
        $subject =  Subject::create([
            'name' => $data['name'],
            'short_name' => $data['short_name'],
            'school_id' => auth()->user()->school_id,
        ]);

        if (isset($data['my_class_id'])) {
            $subject->classes()->attach($data['my_class_id']);
        }


        return session()->flash('success', 'Subject created successfully');
    }

    //update subject

    public function updateSubject(object $subject, $data)
    {
        $subject->name = $data['name'];
        $subject->short_name = $data['short_name'];

        $subject->save();

        if (isset($data['my_class_id'])) {
            $subject->classes()->sync($data['my_class_id']);
        }

        return session()->flash('success', 'Subject updated successfully');
    }

    //delete subject

    public function deleteSubject($id)
    {
        return Subject::where(['id' => $id])->delete();
    }
}
