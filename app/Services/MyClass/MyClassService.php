<?php

namespace App\Services\MyClass;

use App\Exceptions\InvalidValueException;
use App\Exceptions\ResourceNotEmptyException;
use App\Models\ClassGroup;
use App\Models\MyClass;
use App\Services\School\SchoolService;
use Illuminate\Database\Eloquent\Collection;

class MyClassService
{
    /**
     * School service variable.
     */
    public SchoolService $schoolService;

    // construct method
    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    /**
     * Get all classes in school.
     */
    public function getAllClasses(): Collection
    {
        return $this->schoolService->getSchoolById(current_school_id())->myClasses->load('classGroup', 'sections');
    }

    /**
     * Get all ClassGroups in school.
     */
    public function getAllClassGroups(): Collection
    {
        return ClassGroup::inSchool()->get();
    }

    /**
     * Get all classes in school.
     */
    public function getClassById(int $id): MyClass
    {
        return MyClass::find($id);
    }

    /**
     * Get class by id or else return 404.
     */
    public function getClassByIdOrFail(int $id)
    {
        return $this->schoolService->getSchoolById(current_school_id())->myClasses()->findOrFail($id);
    }

    /**
     * Get class group by id.
     */
    public function getClassGroupById(int $id)
    {
        return ClassGroup::inSchool()->find($id);
    }

    /**
     * Create new class.
     *
     * @param array|object $record
     *
     * @return MyClass
     */
    public function createClass($record)
    {
        $classGroup = $this->getClassGroupById($record['class_group_id']);
        if ($classGroup->school->id != current_school_id()) {
            throw new InvalidValueException('ClassGroup Is Not In Class');
        }
        $myClass = MyClass::create($record);

        return $myClass;
    }

    /**
     * Create new class group.
     *
     * @param array|object $record
     *
     * @return ClassGroup
     */
    public function createClassGroup($record)
    {
        $classGroup = ClassGroup::create($record);

        return $classGroup;
    }

    /**
     * Update class.
     *
     * @param MyClass      $class
     * @param array|object $records
     *
     * @return MyClass
     */
    public function updateClass($class, $records)
    {
        $class->update([
            'name'           => $records['name'],
            'class_group_id' => $records['class_group_id'],
        ]);

        return $class;
    }

    /**
     * Update class group.
     *
     * @param array|object $records
     *
     * @return ClassGroup
     */
    public function updateClassGroup(ClassGroup $classGroup, $records)
    {
        $classGroup->update(
            [
                'name' => $records['name'],
            ]
        );

        return $classGroup;
    }

    /**
     * Delete class group.
     *
     *
     *
     * @throws ResourceNotEmptyException
     *
     * @return void
     */
    public function deleteClassGroup(ClassGroup $classGroup)
    {
        if ($classGroup->classes->count()) {
            throw new ResourceNotEmptyException('Class Group contains classes');
        }
        $classGroup->delete();
    }

    /**
     * Delete class.
     *
     *
     *
     * @throws ResourceNotEmptyException
     *
     * @return void
     */
    public function deleteClass(MyClass $class)
    {
        if ($class->studentRecords->count()) {
            throw new ResourceNotEmptyException('Class contains students');
        }
        $class->delete();
    }
}
