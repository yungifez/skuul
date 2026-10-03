<?php

namespace App\Livewire;

use App\Models\ParentRecord;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class ShowParentProfile extends Component
{
    public User $parent;

    public function render(): View
    {
        return view('livewire.show-parent-profile', [
            'children' => $this->children(),
        ]);
    }

    /**
     * Get the learners at this school that the parent is linked to.
     *
     * @return Collection<int, User>
     */
    private function children(): Collection
    {
        $parentRecord = ParentRecord::query()->firstWhere('user_id', $this->parent->id);

        if ($parentRecord === null) {
            return collect();
        }

        return $parentRecord->studentsInSchool()
            ->with('studentRecord.academicCycleSection.academicLevel')
            ->orderBy('name')
            ->get();
    }
}
