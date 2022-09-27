<?php

namespace App\Services\GradeSystem;

use App\Models\GradeSystem;

class GradeSystemService
{
    /**
     * Get all grades in class group.
     *
     * @param int $classGroup_id
     *
     * @return App\Model\GradeSystem
     */
    public function getAllGradesInClassGroup(int $classGroup_id)
    {
        $grades = GradeSystem::where('class_group_id', $classGroup_id)->get();

        return $grades;
    }

    /**
     * Get grade in classgroup for a percent.
     *
     * @param int $classGroup
     * @param int $percentage
     *
     * @return void
     */
    public function getGrade(int $classGroup, int $percentage)
    {
        return $this->getAllGradesInClassGroup($classGroup)->where('grade_from', '<=', $percentage)->where('grade_till', '>=', $percentage)->first();
    }

    /**
     * Create grade in gradesystem.
     *
     * @param array|object $records
     *
     * @return void
     */
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
        session()->flash('success', 'Grade system created successfully');
    }

    /**
     * Update frade in gradesystem.
     *
     * @param GradeSystem  $grade
     * @param array|object $records
     *
     * @return void
     */
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
        session()->flash('success', 'Grade updated successfully');
    }

    /**
     * Delete Grade in grade system.
     *
     * @param GradeSystem $grade
     *
     * @return void
     */
    public function deleteGradeSystem(GradeSystem $grade)
    {
        $grade->delete();
        session()->flash('success', 'successfully deleted grade');
    }

    /**
     * @param array $grade  with grade_from and grade_till
     * @param array $grades each with grade_from and grade_till (testing against)
     *
     * @return bool
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
