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

    //get grade from percent

    public function getGrade($classGroup, $percentage)
    {
        return $this->getAllGradesInClassGroup($classGroup)->where('grade_from', '<=', $percentage)->where('grade_till', '>=', $percentage)->first();
    }

    //create grade system

    public function createGradeSystem($records)
    {
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id']);

        if ($gradesInDb = $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_till' => $records['grade_till']], $gradesInDb)) {
            return session()->flash('danger', 'Grade is in another range in this class group');
        }

        GradeSystem::create([
            'class_group_id' => $records['class_group_id'],
            'grade_from'     => $records['grade_from'],
            'grade_till'     => $records['grade_till'],
            'name'           => $records['name'],
            'remark'         => $records['remark'],
        ]);

        return session()->flash('success', 'Grade system created successfully');
    }

    // edit grade system

    public function updateGradeSystem(GradeSystem $grade, $records)
    {
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id'])->except($grade->id);

        if ($gradesInDb = $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_till' => $records['grade_till']], $gradesInDb)) {
            return session()->flash('danger', 'Grade is in another range in this class group');
        }

        $grade->update([
            'class_group_id' => $records['class_group_id'],
            'grade_from'     => $records['grade_from'],
            'grade_till'     => $records['grade_till'],
            'name'           => $records['name'],
            'remark'         => $records['remark'],
        ]);
        $grade->save();

        return session()->flash('success', 'Grade updated successfully');
    }

    public function deleteGradeSystem(GradeSystem $grade)
    {
        $grade->delete();

        return session()->flash('success', 'successfully deleted grade');
    }

    /**
     * @param array $grade  with grade_from and grade_till
     * @param array $grades each with grade_from and grade_till (testing against)
     */
    public function gradeRangeExists($grade, $grades)
    {
        foreach ($grades as $i) {
            //check if given grade is in range of grade in array
            if ($grade['grade_from'] >= $i['grade_from'] && $grade['grade_till'] <= $i['grade_till']) {
                return true;
            }
            //check if array grade is in range of given grade
            if ($i['grade_from'] >= $grade['grade_from'] && $i['grade_till'] <= $grade['grade_till']) {
                return true;
            }
            //check if given grade starts at array grade
            if (in_array($grade['grade_from'], range($i['grade_from'], $i['grade_till']))) {
                return true;
            }
            //check if given grade ends at array grade
            if (in_array($grade['grade_till'], range($i['grade_from'], $i['grade_till']))) {
                return true;
            }
        }

        return false;
    }
}
