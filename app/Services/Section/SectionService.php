<?php

namespace App\Services\Section;

use App\Models\Section;
use App\Services\School\SchoolService;

class SectionService
{
    //construct method which calls on the SchoolService class

    public function __construct(SchoolService $school)
    {
        $this->school = $school;
    }
    
    public function getAllSections()
    {
        $myClasses = $this->school->getSchoolById(auth()->user()->school_id)->myClasses->all();

        try {
            $sections = collect();
            foreach ($myClasses as $myClass) {
                $sections = $sections->merge($myClass->sections->all());
            }
            return $sections;
        } catch (\Throwable $th) {
            return session()->flash('danger' ,__('No sections found'));
        }
    }

    public function getSectionById($id)
    {
        return Section::find($id);
    }

    public function createSection($records)
    {

        if (!$this->getSectionById($records['my_class_id'])) {
            session()->flash('danger' ,__('Class does not exists'));
            return;
        }

        $section = Section::firstOrCreate($records);

        if ($section->wasRecentlyCreated) {
            session()->flash('success' ,__('Section created successfully'));
        }else {
            session()->flash('danger' ,__('Section already exists'));
        }

        return $section;
    }

    public function updateSection(Section $section, $record)
    {
        $section->name = $record->name;
        $section->save();
        session()->flash('success' ,__('Section updated successfully'));

        return $section;
    }
}