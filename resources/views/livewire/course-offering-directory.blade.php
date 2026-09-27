<div class="flex flex-col gap-6">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label for="offering-year" class="sr-only">{{ school_term('academic_year', 'School year') }}</label>
            <select id="offering-year" wire:model.live="academicYearId" class="{{ $controlClasses }}">
                <option value="">All {{ strtolower(school_terms('academic_year', 'school years')) }}</option>
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="offering-subject" class="sr-only">{{ school_term('course', 'Subject') }}</label>
            <select id="offering-subject" wire:model.live="subjectId" class="{{ $controlClasses }}">
                <option value="">All {{ strtolower(school_terms('course', 'subjects')) }}</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($courseOfferings->isEmpty())
        <p class="text-sm text-muted-foreground">No {{ strtolower(school_terms('course', 'subjects')) }}</p>
    @else
        <ul class="divide-y border-y" wire:loading.class="opacity-60" wire:target="academicYearId,subjectId,gotoPage,nextPage,previousPage">
            @foreach ($courseOfferings as $courseOffering)
                @php
                    $subjectName = $courseOffering->subject->name;
                    $canUpdate = auth()->user()->can('update', $courseOffering);
                    $canOpenGradebook = auth()->user()->can('viewGradebook', $courseOffering);
                    $isDraft = $courseOffering->status === \App\Enums\CourseOfferingStatus::Draft;
                    $isArchived = $courseOffering->status === \App\Enums\CourseOfferingStatus::Archived;
                    $roster = match (true) {
                        $courseOffering->roster_mode->usesHomeSections() => $courseOffering->cycleSections->map(fn ($section) => $section->label ?? $section->name)->join(', '),
                        $courseOffering->roster_mode === \App\Enums\RosterMode::AcademicLevel => school_roster_label($courseOffering->roster_mode),
                        default => $courseOffering->studentRecords->map(fn ($record) => $record->user?->name ?? $record->admission_number)->join(', '),
                    };
                    $teachersLine = $courseOffering->teachingAssignments->map(fn ($assignment) => $assignment->teacher->name)->join(', ');
                @endphp
                <li wire:key="offering-{{ $courseOffering->id }}" class="flex flex-col gap-3 py-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 text-sm">
                            <p class="truncate font-medium">
                                @if ($canOpenGradebook)
                                    <a href="{{ route('course-offerings.gradebook.show', $courseOffering) }}" class="underline-offset-4 hover:underline">{{ $subjectName }}</a>
                                @else
                                    {{ $subjectName }}
                                @endif
                                <span class="font-normal text-muted-foreground">· {{ $courseOffering->academicLevel->name }}</span>
                            </p>
                            <p class="truncate text-muted-foreground">{{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }} · {{ $roster !== '' ? $roster : '—' }}</p>
                            <p class="truncate text-muted-foreground">{{ $teachersLine !== '' ? $teachersLine : '—' }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            @if ($isDraft && $canUpdate)
                                <april:button type="button" class="h-11 select-none" wire:click="activate({{ $courseOffering->id }})" wire:loading.attr="disabled" wire:target="activate({{ $courseOffering->id }})">Activate</april:button>
                            @else
                                <span @class(['text-sm', 'text-muted-foreground' => $courseOffering->status !== \App\Enums\CourseOfferingStatus::Active])>{{ $courseOffering->status->label() }}</span>
                            @endif
                            @if ($canUpdate || $canOpenGradebook)
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Actions for {{ $subjectName }}, {{ $courseOffering->academicLevel->name }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content align="end" class="w-52">
                                        @if ($canOpenGradebook)
                                            <april:dropdown-menu-item href="{{ route('course-offerings.gradebook.show', $courseOffering) }}"><x-lucide-book-open class="mr-2 size-4" />Gradebook</april:dropdown-menu-item>
                                        @endif
                                        @if ($canUpdate && !$isArchived)
                                            <april:dropdown-menu-item href="{{ route('course-offerings.edit', $courseOffering) }}"><x-lucide-users class="mr-2 size-4" />Edit roster</april:dropdown-menu-item>
                                        @endif
                                        @if ($canUpdate)
                                            <april:dropdown-menu-item wire:click="startAssigning({{ $courseOffering->id }})"><x-lucide-user-plus class="mr-2 size-4" />Add teacher</april:dropdown-menu-item>
                                        @endif
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>
                    </div>

                    @if ($canUpdate && $assigningId === $courseOffering->id)
                        <form wire:submit="assignTeacher" class="flex flex-col gap-3" aria-label="Add a teacher to {{ $subjectName }}">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="teacher-{{ $courseOffering->id }}" class="sr-only">Teacher</label>
                                    <select id="teacher-{{ $courseOffering->id }}" wire:model="teacherId" class="{{ $controlClasses }}" {{ field_error_bindings('teacherId') }}>
                                        <option value="">{{ $teachers->isEmpty() ? 'No teachers' : 'Choose a teacher' }}</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="teaching-role-{{ $courseOffering->id }}" class="sr-only">Role</label>
                                    <select id="teaching-role-{{ $courseOffering->id }}" wire:model="role" class="{{ $controlClasses }}">
                                        @foreach ($roles as $teachingRole)
                                            <option value="{{ $teachingRole->value }}">{{ $teachingRole->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <x-field-error name="teacherId" />
                            <x-field-error name="role" />
                            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancelAssigning">Cancel</april:button>
                                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="assignTeacher">Add teacher</april:button>
                            </div>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
        {{ $courseOfferings->links('components.datatable-pagination-links-view') }}
    @endif
</div>
