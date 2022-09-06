<?php

namespace App\Services\MyClass;

use App\Models\ClassGroup;
use App\Models\MyClass;
use App\Services\School\SchoolService;

class MyClassService
{
    /**
     * School service variable.
     *
     * @var App\Services\SchoolService
     */
    public $school;

    //construct method
    public function __construct(SchoolService $school)
    {
        $this->school = $school;
    }

    /**
     * Get all classes in school.
     *
     * @return Illuminate\Support\Collection
     */
    public function getAllClasses()
    {
        return collect($this->school->getSchoolById(auth()->user()->school_id)->myClasses->load('classGroup', 'sections')->all());
    }

    /**
     * Get all ClassGroups in school.
     *
     * @return Illuminate\Eloquent\Collection
     */
    public function getAllClassGroups()
    {
        return ClassGroup::where('school_id', auth()->user()->school_id)->get();
    }

    /**
     * Get all classes in school.
     *
     * @param int $id
     *
     * @return App\Models\MyClass
     */
    public function getClassById(int $id)
    {
        return MyClass::find($id);
    }

    /**
     * Get class by id or else return 404.
     *
     * @param int $id
     *
     * @return void
     */
    public function getClassByIdOrFail(int $id)
    {
        return $this->school->getSchoolById(auth()->user()->school_id)->myClasses()->findOrFail($id);
    }

    /**
     * Get class group by id.
     *
     * @param int $id
     *
     * @return void
     */
    public function getClassGroupById(int $id)
    {
        return ClassGroup::where('school_id', auth()->user()->school_id)->find($id);
    }

    /**
     * Create new class.
     *
     * @param array|object $record
     *
     * @return App\Models\MyClass
     */
    public function createClass($record)
    {
        if (!$this->getClassGroupById($record['class_group_id'])) {
            return session()->flash('danger', __('Class group does not exists'));
        }

        $myClass = MyClass::create($record);
        session()->flash('success', __('Class created successfully'));

        return $myClass;
    }

    /**
     * Create new class group.
     *
     * @param array|object $record
     *
     * @return App\Models\ClassGroup
     */
    public function createClassGroup($record)
    {
        $record['school_id'] = auth()->user()->school_id;

        $classGroup = ClassGroup::firstOrCreate($record);

        if (!$classGroup->wasRecentlyCreated) {
            session()->flash('danger', __('Class group already exists'));
        } else {
            session()->flash('success', __('Class group created successfully'));
        }

        return $classGroup;
    }

    /**
     * Update class.
     *
     * @param App\Models\MyClass $myClass
     * @param array|object       $records
     *
     * @return App\Models\MyClass
     */
    public function updateClass($myClass, $records)
    {
        $myClass->update([
            'name'           => $records['name'],
            'class_group_id' => $records['class_group_id'],
        ]);
        session()->flash('success', __('Class updated successfully'));

        return $myClass;
    }

    /**
     * Update class group.
     *
     * @param App\Models\ClassGroup $classGroup
     * @param array|object          $records
     *
     * @return App\Models\ClassGroup
     */
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

    /**
     * Delete class group.
     *
     * @param App\Models\ClassGroup $classGroup
     *
     * @return void
     */
    public function deleteClassGroup(ClassGroup $classGroup)
    {
        if ($classGroup->classes->count()) {
            return session()->flash('danger', __('Remove all classes from this class group first'));
        }
        $classGroup->delete();
        session()->flash('success', __('Class group deleted successfully'));
    }

    /**
     * Delete class.
     *
     * @param App\Models\MyClass $class
     *
     * @return void
     */
    public function deleteClass(MyClass $class)
    {
        if ($class->studentRecords->count()) {
            return session()->flash('danger', __('Remove all students from this class first'));
        }
        $class->delete();
        session()->flash('success', __('Class deleted successfully'));
    }
}
