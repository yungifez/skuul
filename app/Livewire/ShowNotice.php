<?php

namespace App\Livewire;

use App\Actions\Notice\PublishNotice;
use App\Enums\NoticeAudienceScope;
use App\Exceptions\InvalidValueException;
use App\Livewire\Concerns\DispatchesStatusNotifications;
use App\Models\AcademicCycleSection;
use App\Models\AcademicLevel;
use App\Models\CourseOffering;
use App\Models\Notice;
use App\Services\Notice\NoticeContentSanitizer;
use Illuminate\View\View;
use Livewire\Component;

class ShowNotice extends Component
{
    use DispatchesStatusNotifications;

    public Notice $notice;

    public function publishNotice(PublishNotice $publishNotice): void
    {
        $this->authorize('update', $this->notice);

        try {
            $this->notice = $publishNotice->publish($this->notice, auth()->user());
            $this->notify('Notice published successfully');
        } catch (InvalidValueException $exception) {
            $this->addError('notice', $exception->getMessage());
        }
    }

    public function render(NoticeContentSanitizer $contentSanitizer): View
    {
        return view('livewire.show-notice', [
            'content' => $contentSanitizer->sanitize((string) $this->notice->content),
            'audience' => auth()->user()?->can('update', $this->notice) ? $this->audienceSummary() : null,
        ]);
    }

    /**
     * Say who the notice reaches, for the people who manage it.
     */
    private function audienceSummary(): string
    {
        /** @var array{scope?: string, academic_level_ids?: array<int, int>, academic_cycle_section_ids?: array<int, int>, course_offering_ids?: array<int, int>, include_guardians?: bool}|null $audience */
        $audience = $this->notice->audience;
        $scope = NoticeAudienceScope::tryFrom((string) ($audience['scope'] ?? '')) ?? NoticeAudienceScope::School;

        $names = match ($scope) {
            NoticeAudienceScope::School => [],
            NoticeAudienceScope::Classes => AcademicLevel::query()->inSchool()->whereKey($audience['academic_level_ids'] ?? [])->orderBy('name')->pluck('name')->all(),
            NoticeAudienceScope::Section => AcademicCycleSection::query()->inSchool()->whereKey($audience['academic_cycle_section_ids'] ?? [])->with('academicLevel:id,name')->get()
                ->map(fn (AcademicCycleSection $section): string => trim($section->academicLevel->name.' · '.($section->label ?? $section->name), ' ·'))
                ->sort()
                ->all(),
            NoticeAudienceScope::Offering => CourseOffering::query()->inSchool()->whereKey($audience['course_offering_ids'] ?? [])->with(['subject:id,name', 'academicLevel:id,name'])->get()
                ->map(fn (CourseOffering $offering): string => $offering->qualifiedName())
                ->sort()
                ->all(),
        };

        $summary = $names === [] ? $scope->label() : $scope->label().': '.implode(', ', $names);

        return ($audience['include_guardians'] ?? false) ? $summary.', with guardians' : $summary;
    }
}
