<?php

namespace App\Services\GradeSystem;

use App\Models\GradeSystem;
use App\Exceptions\DuplicateRangeException;

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
        return GradeSystem::where([['grade_from', '<=', $percentage], ['grade_till', '>=', $percentage], ['class_group_id', $classGroup]])->first();
    }

    /**
     * Create grade in gradesystem.
     *
     * @param array|object $records
     * 
     * @throws DuplicateRangeException
     *
     * @return void
     */
    public function createGradeSystem($records)
    {
        //get all grades in the class group
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id']);


        if ($this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_till' => $records['grade_till']], $gradesInDb)) {
            throw new DuplicateRangeException("Grade range is in another range in class group");
        }

        GradeSystem::create([
            'class_group_id' => $records['class_group_id'],
            'grade_from'     => $records['grade_from'],
            'grade_till'     => $records['grade_till'],
            'name'           => $records['name'],
            'remark'         => $records['remark'],
        ]);
    }

    /**
     * Update frade in gradesystem.
     *
     * @param GradeSystem  $grade
     * @param array|object $records
     * 
     * @throws DuplicateRangeException
     *
     * @return void
     */
    public function updateGradeSystem(GradeSystem $grade, $records)
    {
        $gradesInDb = $this->getAllGradesInClassGroup($records['class_group_id'])->except($grade->id);

        if ($gradesInDb = $this->gradeRangeExists(['grade_from' => $records['grade_from'], 'grade_till' => $records['grade_till']], $gradesInDb)) {
           throw new DuplicateRangeException("Grade range is in another range in class group");
        }

        $grade->update([
            'class_group_id' => $records['class_group_id'],
            'grade_from'     => $records['grade_from'],
            'grade_till'     => $records['grade_till'],
            'name'           => $records['name'],
            'remark'         => $records['remark'],
        ]);
        $grade->save();
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
