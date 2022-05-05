<?php

namespace App\Services\GradeSystem;

use App\Models\GradeSystem;

class GradeSystemService
{
    //get all grade systems in class group

    public function getAllGradesInClassGroup($classGroupId)
    {
        $grades = GradeSystem::where('class_group_id', $classGroupId)->get();
        return $grades;
    }

    //create grade system

    public function createGradeSystem($records)
    {
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id']);

        if ($gradesInDb =! null && $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_to' => $records['grade_to']],$gradesInDb)) {
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

    public function updateGradeSystem(GradeSystem $grade, $records)
    {
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id'])->except($grade->id);

        if ($gradesInDb =! null && $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_to' => $records['grade_to']],$gradesInDb)) {
            return session()->flash('danger' , 'Grade is in another range in this class group');
        }
        
        $grade->update([
            'class_group_id' => $records['class_group_id'],
            'grade_from' => $records['grade_from'],
            'grade_to' => $records['grade_to'],
            'name' => $records['name'],
            'remark' => $records['remark']
        ]);
        $grade->save();
        return session()->flash('success' , 'Grade updated successfully');
    }

    public function deleteGradeSystem(GradeSystem $grade)
    {
        $grade->delete();

        return session()->flash('success' , 'successfully deleted grade');
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
