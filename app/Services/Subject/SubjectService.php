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
        $subject =  Subject::firstOrCreate([
            'name' => $data['name'],
            'short_name' => $data['short_name'],
            'school_id' => auth()->user()->school_id,
            'my_class_id' => $data['my_class_id'],
        ]);

        if ($subject->wasRecentlyCreated) {
            return session()->flash('success', 'Subject created successfully');
        }
        
        return session()->flash('danger', 'Subject already exists or something went wrong');
    }

    //update subject

    public function updateSubject(object $subject, $data)
    {
        $subject->name = $data['name'];
        $subject->short_name = $data['short_name'];

        $subject->save();
        
        return session()->flash('success', 'Subject updated successfully');
    }

    //delete subject

    public function deleteSubject($id)
    {
        return Subject::where(['id' => $id])->delete();
    }
}
