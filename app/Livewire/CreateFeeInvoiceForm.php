<?php

namespace App\Livewire;

use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CreateFeeInvoiceForm extends Component
{
    public $feeCategories;

    public int $feeCategory;

    public $fees;

    public $fee = null;

    public $addedFees;

    public $addedStudents;

    public $academicLevels;

    public $academicLevel;

    public $cycleSections;

    public $cycleSection;

    public $students;

    public $student;

    public function mount()
    {
        $this->addedFees = collect();
        $this->addedStudents = collect();
        $this->feeCategories = FeeCategory::inSchool()->get();
        $this->academicLevels = AcademicLevel::inSchool()
            ->whereHas('cycleSections', fn ($query) => $query->where('academic_year_id', current_academic_year_id()))
            ->orderBy('position')
            ->orderBy('name')
            ->get();
        if ($this->academicLevels->isNotEmpty()) {
            $this->academicLevel = $this->academicLevels->first()->id;
            $this->updatedAcademicLevel();
        }

        if ($this->feeCategories->isNotEmpty()) {
            $this->feeCategory = $this->feeCategories->first()->id;
            $this->updatedFeeCategory();
        }

        $this->setOldValues();
    }

    public function updatedAcademicLevel()
    {
        $this->cycleSections = AcademicCycleSection::inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->where('academic_level_id', $this->academicLevel)
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        if ($this->cycleSections->isNotEmpty()) {
            $this->cycleSection = $this->cycleSections->first()->id;
            $this->updatedCycleSection();
        } else {
            $this->cycleSections = null;
            $this->students = null;
        }
    }

    public function updatedCycleSection()
    {
        if ($this->cycleSection == null) {
            $this->students = null;
            $this->student = null;

            return;
        }

        $this->students = $this->studentsOfSections([$this->cycleSection]);
        if ($this->students->isNotEmpty()) {
            $this->student = $this->students->first()->id;
        }
    }

    public function updatedFeeCategory()
    {
        $this->fees = $this->feeCategories->find($this->feeCategory)->fees;
        if ($this->fees != null && !$this->fees->isEmpty()) {
            $this->fee = $this->fees->first()->id;
        }
    }

    public function addFee(FeeCategory $feeCategory, $fee = 0)
    {
        $fee = Fee::find($fee);

        if ($fee == null || !$fee->exists()) {
            $this->addedFees = $this->addedFees->merge($feeCategory->fees);
        } else {
            $this->addedFees = $this->addedFees->push($fee);
        }

        $this->addedFees = $this->addedFees->unique('id');
    }

    /**
     * Add one student, a whole cycle section, or a whole academic level.
     */
    public function addStudent(AcademicLevel $academicLevel, $cycleSection = null, $student = null)
    {
        $student = User::students()->ofSchool()->find($student);

        if ($student != null && $student->exists()) {
            $this->addedStudents = $this->addedStudents->push($student->load('studentRecord'));
            $this->addedStudents = $this->addedStudents->keyBy('id');

            return;
        }

        $sectionIds = AcademicCycleSection::inSchool()
            ->where('academic_year_id', current_academic_year_id())
            ->when(
                $cycleSection != null,
                fn ($query) => $query->whereKey($cycleSection),
                fn ($query) => $query->where('academic_level_id', $academicLevel->id),
            )
            ->pluck('id')
            ->all();

        $this->addedStudents = $this->addedStudents->merge($this->studentsOfSections($sectionIds));
        $this->addedStudents = $this->addedStudents->keyBy('id');
    }

    /**
     * Get the active students placed in any of the given cycle sections.
     *
     * @param  array<int, int>  $cycleSectionIds
     * @return Collection<int, User>
     */
    private function studentsOfSections(array $cycleSectionIds)
    {
        return User::activeStudents()
            ->whereHas('studentRecord', fn ($query) => $query->whereIn('academic_cycle_section_id', $cycleSectionIds))
            ->with('studentRecord')
            ->orderBy('name')
            ->get();
    }

    public function removeStudent($student)
    {
        $this->addedStudents->forget($student);
    }

    public function removeFee($fee)
    {
        $this->addedFees->forget($fee);
    }

    public function setOldValues()
    {
        $oldRecords = collect(old('records'));
        if ($oldRecords->isNotEmpty()) {
            $fees = Fee::whereRelation('feeCategory', 'school_id', current_school_id())->whereIn('id', $oldRecords->pluck('fee_id'))->get();

            $this->addedFees = $this->addedFees->merge($fees);

            $this->addedFees = $this->addedFees->keyBy('id');
        }

        $oldStudents = collect(old('users'));
        if ($oldStudents->isNotEmpty()) {
            $students = User::students()->ofSchool()->whereIn('id', $oldStudents)->get();

            $this->addedStudents = $this->addedStudents->merge($students);
        }
        $this->addedFees = $this->addedFees->unique('id');
    }

    public function render()
    {
        return view('livewire.create-fee-invoice-form');
    }
}
