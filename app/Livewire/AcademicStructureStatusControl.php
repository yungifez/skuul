<?php

namespace App\Livewire;

use App\Actions\Curriculum\ChangeAcademicCycleSectionStatus;
use App\Actions\Curriculum\ChangeAcademicLevelStatus;
use App\Enums\AcademicStructureStatus;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Show whether a class or section is in use, and activate or archive it.
 *
 * Archiving keeps every record that names it. Only new work stops.
 */
class AcademicStructureStatusControl extends Component
{
    use DispatchesStatusNotifications;

    public AcademicLevel|AcademicCycleSection $record;

    public function activate(): void
    {
        $this->moveTo(AcademicStructureStatus::Active);
    }

    public function archive(): void
    {
        $this->moveTo(AcademicStructureStatus::Archived);
    }

    public function render(): View
    {
        $status = $this->record->status;
        $canUpdate = Gate::allows('update', $this->record);
        $isLevel = $this->record instanceof AcademicLevel;

        return view('livewire.academic-structure-status-control', [
            'status' => $status,
            'canActivate' => $canUpdate && $status->canMoveTo(AcademicStructureStatus::Active),
            'canArchive' => $canUpdate && $status->canMoveTo(AcademicStructureStatus::Archived),
            'archiveWarning' => $isLevel
                ? "Archive {$this->record->name}? No new ".strtolower(school_terms('section', 'sections')).' can be added to it. Everything already recorded keeps its name.'
                : 'Archive '.($this->record->label ?? $this->record->name).'? It leaves new work for this year. Everything already recorded stays readable.',
        ]);
    }

    private function moveTo(AcademicStructureStatus $status): void
    {
        Gate::authorize('update', $this->record);

        try {
            $this->record = $this->record instanceof AcademicLevel
                ? app(ChangeAcademicLevelStatus::class)->change($this->record, $status, auth()->user())
                : app(ChangeAcademicCycleSectionStatus::class)->change($this->record, $status, auth()->user());
        } catch (InvalidValueException $exception) {
            $this->notify($exception->getMessage(), 'danger');

            return;
        }

        session()->flash('success', "Now {$status->label()}.");
        $this->redirect(url()->previous(), navigate: false);
    }
}
