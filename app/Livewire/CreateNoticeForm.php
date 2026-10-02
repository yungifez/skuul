<?php

namespace App\Livewire;

use App\Enums\AcademicStructureStatus;
use App\Enums\NoticeAudienceScope;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\Notice;
use App\Services\Notice\NoticeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Write a notice as a draft. It is read and published from its own page.
 */
class CreateNoticeForm extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $content = '';

    public string $startDate = '';

    public string $stopDate = '';

    /** @var UploadedFile|null */
    public $attachment = null;

    public string $audienceScope = 'school';

    /** @var array<int, string> */
    public array $academicLevelIds = [];

    /** @var array<int, string> */
    public array $sectionIds = [];

    public bool $includeGuardians = false;

    public function mount(): void
    {
        Gate::authorize('create', Notice::class);

        $this->startDate = school_today()->toDateString();
        $this->stopDate = now()->addWeeks(2)->toDateString();
    }

    public function save(NoticeService $noticeService): void
    {
        Gate::authorize('create', Notice::class);

        $this->title = trim($this->title);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:65000'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx', 'max:10240'],
            'startDate' => ['required', 'date'],
            'stopDate' => ['required', 'date', 'after_or_equal:startDate'],
            'audienceScope' => ['required', Rule::in(NoticeAudienceScope::values())],
            'academicLevelIds' => [Rule::requiredIf($this->audienceScope === NoticeAudienceScope::Classes->value), 'array'],
            'academicLevelIds.*' => ['integer', 'distinct', Rule::exists((new AcademicLevel)->getTable(), 'id')->where('school_id', current_school_id())],
            'sectionIds' => [Rule::requiredIf($this->audienceScope === NoticeAudienceScope::Section->value), 'array'],
            'sectionIds.*' => ['integer', 'distinct', Rule::exists((new AcademicCycleSection)->getTable(), 'id')->where('school_id', current_school_id())],
            'includeGuardians' => ['boolean'],
        ], [
            'academicLevelIds.required' => 'Choose at least one class or level.',
            'sectionIds.required' => 'Choose at least one section.',
            'content.required' => 'Write the message.',
        ], [
            'startDate' => 'start date',
            'stopDate' => 'end date',
            'audienceScope' => 'audience',
        ]);

        // A stripped editor can hold markup with no words, such as an empty paragraph.
        if (trim(strip_tags($this->content)) === '') {
            $this->addError('content', 'Write the message.');

            return;
        }

        $notice = $noticeService->storeNotice([
            'title' => $this->title,
            'content' => $this->content,
            'start_date' => $this->startDate,
            'stop_date' => $this->stopDate,
            'attachment' => $this->attachment,
            'audience' => [
                'scope' => $this->audienceScope,
                'academic_level_ids' => $this->audienceScope === NoticeAudienceScope::Classes->value ? array_map('intval', $this->academicLevelIds) : [],
                'academic_cycle_section_ids' => $this->audienceScope === NoticeAudienceScope::Section->value ? array_map('intval', $this->sectionIds) : [],
                'include_guardians' => $this->includeGuardians,
            ],
        ]);

        session()->flash('success', 'The notice was saved as a draft. Read it, then publish it.');
        $this->redirectRoute('notices.show', $notice);
    }

    public function render(): View
    {
        return view('livewire.create-notice-form', [
            'academicLevels' => AcademicLevel::query()
                ->inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'is_group', 'name']),
            'audienceScopes' => NoticeAudienceScope::cases(),
            'sections' => AcademicCycleSection::query()
                ->inSchool()
                ->where('status', AcademicStructureStatus::Active)
                ->with('academicLevel:id,name')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
