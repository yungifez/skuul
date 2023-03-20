<?php

namespace App\Services\Section;

use App\Exceptions\ResourceNotEmptyException;
use App\Models\Section;
use App\Services\School\SchoolService;

class SectionService
{
    /**
     * School service instance.
     */
    public SchoolService $school;

    public function __construct(SchoolService $school)
    {
        $this->school = $school;
    }

    /**
     * Get all sections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllSections()
    {
        $myClasses = $this->school->getSchoolById(auth()->user()->school_id)->myClasses->load('sections')->all();
        $sections = collect();
        foreach ($myClasses as $myClass) {
            $sections = $sections->merge($myClass->sections->all());
        }

        return $sections;
    }

    /**
     * Get a section by Id.
     *
     *
     * @return \App\Models\Section
     */
    public function getSectionById(int $id)
    {
        return Section::find($id);
    }

    /**
     * Create section.
     *
     * @param mixed $records
     *
     * @return void
     */
    public function createSection($records)
    {
        $section = Section::create($records);

        return $section;
    }

    /**
     * Update section.
     *
     * @param mixed $record
     *
     * @return void
     */
    public function updateSection(Section $section, $record)
    {
        $section->name = $record->name;
        $section->save();

        return $section;
    }

    /**
     * Delete section.
     *
     *
     * @return void
     */
    public function deleteSection(Section $section)
    {
        if ($section->studentRecords->count() > 0) {
            throw new ResourceNotEmptyException('There are students in this section');
        }
        $section->delete();
    }
}
