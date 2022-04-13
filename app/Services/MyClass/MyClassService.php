<?php

namespace App\Services\MyClass;

use App\Models\ClassGroup;
use App\Models\MyClass;
use App\Services\School\SchoolService;

class MyClassService
{
    //create public properties
    public $school;

    //construct method
    public function __construct(SchoolService $school)
    {
        $this->school = $school;
    }

    public function getAllClasses()
    {
        return collect($this->school->getSchoolById(auth()->user()->school_id)->myClasses->load('classGroup', 'sections')->all());
    }

    public function getAllClassGroups()
    {
        return ClassGroup::where('school_id', auth()->user()->school_id)->get();
    }

    public function getClassById($id)
    {
        return MyClass::find($id);
    }

    public function getClassByIdOrFail($id)
    {
        return $this->school->getSchoolById(auth()->user()->school_id)->myClasses()->findOrFail($id);
    }

    public function getClassGroupById($id)
    {
        return ClassGroup::where('school_id', auth()->user()->school_id)->find($id);
    }

    public function createClass($record)
    {
        if (! $this->getClassGroupById($record['class_group_id'])) {
            return session()->flash('danger', __('Class group does not exists'));
        }

        $myClass = MyClass::firstOrCreate($record);

        if (! $myClass->wasRecentlyCreated) {
            session()->flash('danger', __('Class already exists'));
        } else {
            session()->flash('success', __('Class created successfully'));
        }

        return $myClass;
    }

    public function createClassGroup($record)
    {
        $record['school_id'] = auth()->user()->school_id;

        $classGroup = ClassGroup::firstOrCreate($record);

        if (! $classGroup->wasRecentlyCreated) {
            session()->flash('danger', __('Class group already exists'));
        } else {
            session()->flash('success', __('Class group created successfully'));
        }

        return $classGroup;
    }

    public function updateClass($myClass, $records)
    {
        $myClass->update([
            'name' => $records['name'],
            'class_group_id' => $records['class_group_id'],
        ]);
        session()->flash('success', __('Class updated successfully'));

        return $myClass;
    }

    public function updateClassGroup(ClassGroup $classGroup, $records)
    {
        $classGroup->update(
            [
                'name' => $records['name'],
            ]
        );
        session()->flash('success', __('Class group updated successfully'));

        return $classGroup;
    }

    public function deleteClassGroup(ClassGroup $classGroup)
    {
        if ($classGroup->classes->count()) {
            return session()->flash('danger',__('Remove all classes from this class group first'));
        }
        $classGroup->delete();

        return session()->flash('success', __('Class group deleted successfully'));
    }

    public function deleteClass(MyClass $class)
    {
        if ($class->studentRecords->count()) {
            return session()->flash('danger',__('Remove all students from this class first'));
        }
        $class->delete();

        return session()->flash('success', __('Class deleted successfully'));
    }
}
