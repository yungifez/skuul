<?php

namespace App\Http\Livewire;

use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\MyClass;
use App\Models\Section;
use App\Models\User;
use Livewire\Component;

class CreateFeeInvoiceForm extends Component
{
    public $feeCategories;
    public int $feeCategory;
    public $fees;
    public $fee = null;
    public $addedFees;
    public $addedStudents;
    public $classes;
    public $class;
    public $sections;
    public $section;
    public $students;
    public $student;

    public function mount()
    {
        $this->addedFees = collect();
        $this->addedStudents = collect();
        $this->feeCategories = FeeCategory::where('school_id', auth()->user()->school_id)->get();
        $this->classes = auth()->user()->school->myClasses;
        if ($this->classes->isNotEmpty()) {
            $this->class = $this->classes->first()->id;
            $this->updatedClass();
        }

        if ($this->feeCategories->isNotEmpty()) {
            $this->feeCategory = $this->feeCategories->first()->id;
            $this->updatedFeeCategory();
        }

        $this->setOldValues();
    }

    public function updatedClass()
    {
        $this->sections = $this->classes->find($this->class)->sections;
        if ($this->sections != null && $this->sections->isNotEmpty()) {
            $this->section = $this->sections->first()->id;
            $this->updatedSection();
        } else {
            $this->sections = null;
            $this->students = null;
        }
    }

    public function updatedSection()
    {
        if ($this->section != null) {
            $this->students = $this->sections->find($this->section)?->students();
            if ($this->students != null && $this->students->isNotEmpty()) {
                $this->student = $this->students->first()->id;
            }
        } else {
            $this->students = null;
            $this->student = null;
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

    public function addStudent(MyClass $class, $section = null, $student = null)
    {
        $section = Section::find($section);
        $student = User::students()->inSchool()->find($student);

        if ($student != null && $student->exists()) {
            $this->addedStudents = $this->addedStudents->push($student->load('studentRecord'));
        } elseif ($section != null && $section->exists()) {
            $this->addedStudents = $this->addedStudents->merge($section->students()->load('studentRecord'));
        } else {
            $this->addedStudents = $this->addedStudents->merge($class->students()->load('studentRecord'));
        }

        $this->addedStudents = $this->addedStudents->keyBy('id');
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
            $fees = Fee::whereRelation('feeCategory', 'school_id', auth()->user()->school_id)->whereIn('id', $oldRecords->pluck('fee_id'))->get();

            $this->addedFees = $this->addedFees->merge($fees);

            $this->addedFees = $this->addedFees->keyBy('id');
        }

        $oldStudents = collect(old('users'));
        if ($oldStudents->isNotEmpty()) {
            $students = User::students()->inSchool()->whereIn('id', $oldStudents)->get();

            $this->addedStudents = $this->addedStudents->merge($students);
        }
        $this->addedFees = $this->addedFees->unique('id');
    }

    public function render()
    {
        return view('livewire.create-fee-invoice-form');
    }
}
