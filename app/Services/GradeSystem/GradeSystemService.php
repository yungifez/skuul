<?php

namespace App\Services\GradeSystem;

use App\Models\GradeSystem;

class GradeSystemService
{
    //get all grade systems in class group

    public function getAllGradeSystemsInClassGroup($classGroupId)
    {
        $gradeSystems = GradeSystem::where('class_group_id', $classGroupId)->get();
        return $gradeSystems;
    }

    //create grade system

    public function createGradeSystem($records)
    {
        $grades = $this->getAllGradeSystemsInClassGroup($records['class_group_id']);

        if ($grades =! null && $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_to' => $records['grade_to']],$grades)) {
            return session()->flash('danger' , 'Grade is in another range in this class group');
        }
       
        GradeSystem::create([
            'class_group_id' => $records['class_group_id'],
            'grade_from' => $records['grade_from'],
            'grade_to' => $records['grade_to'],
            'name' => $records['name'],
            'remark' => $records['remark']
        ]);
        
        return session()->flash('success' , 'Grade system created successfully');
    }

    // edit grade system

    public function updateGradeSystem(GradeSystem $gradeSystem, $records)
    {
        $grades = $this->getAllGradeSystemsInClassGroup($records['class_group_id']);

        if ($grades =! null && $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_to' => $records['grade_to']],$grades)) {
            return session()->flash('danger' , 'Grade is in another range in this class group');
        }
        
        $gradeSystem->update([
            'class_group_id' => $records['class_group_id'],
            'grade_from' => $records['grade_from'],
            'grade_to' => $records['grade_to'],
            'name' => $records['name'],
            'remark' => $records['remark']
        ]);
        $gradeSystem->save();
        return session()->flash('success' , 'Grade system updated successfully');
    }

    /**
     * @param array $grade with grade_from and grade_to 
     * @param array $grades each with grade_from and grade_to (testing against)
     */

    public function gradeRangeExists($grade, $grades)
    {
        foreach ($grades as $i) {
            //check if given grade is in range of grade in array
            if ($grade['grade_from'] >= $i['grade_from'] && $grade['grade_to'] <= $i['grade_to']) {
                return true;
            }
            //check if array grade is in range of given grade
            if ($i['grade_from'] >= $grade['grade_from'] && $i['grade_to'] <= $grade['grade_to']) {
                return true;
            }
            //check if given grade starts at array grade 
            if (in_array($grade['grade_from'], range($i['grade_from'], $i['grade_to']))) {
                return true;
            }
            //check if given grade ends at array grade
            if (in_array($grade['grade_to'], range($i['grade_from'], $i['grade_to']))) {
                return true;
            }
        }
        return false;
    }
}
