<?php

namespace App\Services\Section;

use App\Models\Section;

class SectionService
{
    public function getAllSections()
    {
        return Section::where('');
    }

    public function getSectionById($id)
    {
        return Section::find($id);
    }

    public function createSection($records)
    {
        if (!$this->getSectionById($records['my_class_id'])) {
           return  session()->flash('danger' ,__('Class does not exists'));; 
        }

        $section = Section::firstOrCreate($records);

        if ($section->wasRecentlyCreated) {
            session()->flash('danger' ,__('Section created successfully'));
        }else {
            session()->flash('danger' ,__('Section slready exists'));
        }

        return $section;
    }
}