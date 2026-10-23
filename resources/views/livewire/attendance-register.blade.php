<div class="space-y-6">
    <x-display-validation-errors />

    @if ($feedback)
        <div role="status" class="rounded-lg border border-border bg-muted/40 px-4 py-3 text-sm text-foreground">{{ $feedback }}</div>
    @endif
    <x-field-error name="register" />

    @php
        $isLesson = $registerType === 'lesson';
        $selectedTime = $isLesson ? $lessonTimes->firstWhere('id', (int) $timeSlotId) : null;
        $opened = $isLesson ? $offering : $section;
        $registerTitle = $isLesson
            ? ($offering?->qualifiedName().($selectedTime ? ' · '.$selectedTime->start_time.'–'.$selectedTime->stop_time : ''))
            : ($section ? $section->academicLevel?->name.' · '.($section->label ?? $section->name) : null);
    @endphp

    <april:card>
        <slot:title>Open a register</slot:title>
        <slot:description>{{ $isLesson ? 'Choose a subject you teach and a day.' : 'Choose '.with_indefinite_article(strtolower(school_term('section', 'section'))).' and a day.' }}</slot:description>
        <slot:content>
            @if ($lessonsEnabled)
                <div class="mb-4 flex flex-col gap-2 sm:w-72">
                    <april:label for="register-type">Register</april:label>
                    <select id="register-type" wire:model.live="registerType" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="daily">The daily register of {{ with_indefinite_article(strtolower(school_term('section', 'section'))) }}</option>
                        <option value="lesson">The register of one lesson</option>
                    </select>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-[minmax(0,2fr)_minmax(12rem,1fr)] md:items-end">
                @if ($isLesson)
                    <div class="grid gap-4 sm:grid-cols-[minmax(0,2fr)_minmax(10rem,1fr)]">
                        <div class="flex flex-col gap-2">
                            <april:label for="register-offering">Subject</april:label>
                            <select id="register-offering" wire:model.live="courseOfferingId" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('courseOfferingId') }}>
                                <option value="">Choose a subject</option>
                                @foreach ($offerings as $item)
                                    <option value="{{ $item->id }}">{{ $item->qualifiedName() }}</option>
                                @endforeach
                            </select>
                            <x-field-error name="courseOfferingId" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <april:label for="register-time">Lesson</april:label>
                            <select id="register-time" wire:model.live="timeSlotId" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('timeSlotId') }}>
                                <option value="">The whole day</option>
                                @foreach ($lessonTimes as $time)
                                    <option value="{{ $time->id }}">{{ $time->start_time }}–{{ $time->stop_time }}</option>
                                @endforeach
                            </select>
                            <x-field-error name="timeSlotId" />
                        </div>
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <april:label for="register-section">{{ school_term('section', 'Section') }}</april:label>
                        <select id="register-section" wire:model.live="academicCycleSectionId" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('academicCycleSectionId') }}>
                            <option value="">Choose {{ with_indefinite_article(strtolower(school_term('section', 'section'))) }}</option>
                            @foreach ($sections as $item)
                                <option value="{{ $item->id }}">{{ $item->academicLevel?->name }} · {{ $item->label ?? $item->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="academicCycleSectionId" />
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <april:label for="register-date">Day</april:label>
                    <input id="register-date" type="date" wire:model.live="attendedOn" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm" {{ field_error_bindings('attendedOn') }}>
                    <x-field-error name="attendedOn" />
                </div>
            </div>

            @if ($opened)
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-border pt-4">
                    <april:button type="button" variant="outline" wire:click="moveDay(-1)">
                        <x-lucide-chevron-left class="mr-1 size-4" /> Previous day
                    </april:button>
                    <april:button type="button" variant="outline" wire:click="goToToday">Today</april:button>
                    <april:button type="button" variant="outline" wire:click="moveDay(1)" :disabled="$isTodayOrLater">
                        Next day <x-lucide-chevron-right class="ml-1 size-4" />
                    </april:button>
                    <span class="w-full text-sm text-muted-foreground sm:ml-auto sm:w-auto">{{ \Illuminate\Support\Carbon::parse($attendedOn)->format('l, j F Y') }}</span>
                </div>
            @endif
        </slot:content>
    </april:card>

    @if ($isLesson && ! $offering && $offerings->isEmpty())
        <april:card>
            <slot:content>
                <x-empty-state icon="lucide-clipboard-list" title="No subjects to register"
                    description="A lesson register lists the learners of a subject you teach. Ask for a teaching assignment first." />
            </slot:content>
        </april:card>
    @elseif ($isLesson && ! $offering)
        <april:card>
            <slot:content>
                <x-empty-state icon="lucide-clipboard-list" title="No register open"
                    description="Choose a subject above to mark who came to the lesson." />
            </slot:content>
        </april:card>
    @elseif ($isLesson && $students->isEmpty())
        <april:card>
            <slot:title>{{ $registerTitle }}</slot:title>
            <slot:content>
                <x-empty-state icon="lucide-users" title="Nobody takes this subject yet"
                    description="Add learners to the subject's roster, then the register will list them." />
            </slot:content>
        </april:card>
    @elseif ($isLesson)
        @include('livewire.partials.attendance-register-form')
    @elseif (! $section && $sections->isEmpty())
        <april:card>
            <slot:content>
                <x-empty-state icon="lucide-clipboard-list" title="No {{ strtolower(school_terms('section', 'sections')) }} this {{ strtolower(school_term('academic_year', 'school year')) }}"
                    description="A register lists the learners of one {{ strtolower(school_term('section', 'section')) }}. Add and activate {{ with_indefinite_article(strtolower(school_term('section', 'section'))) }} first.">
                    @can('create', App\Models\AcademicCycleSection::class)
                        <april:button-link href="{{ route('academic-cycle-sections.create') }}" variant="outline" class="h-11 select-none">Add {{ with_indefinite_article(strtolower(school_term('section', 'section'))) }}</april:button-link>
                    @endcan
                </x-empty-state>
            </slot:content>
        </april:card>
    @elseif (! $section)
        <april:card>
            <slot:content>
                <x-empty-state icon="lucide-clipboard-list" title="No register open"
                    description="Choose {{ with_indefinite_article(strtolower(school_term('section', 'section'))) }} above to mark who attended." />
            </slot:content>
        </april:card>
    @elseif ($closure)
        <april:card>
            <slot:title>{{ $section->academicLevel?->name }} · {{ $section->label ?? $section->name }}</slot:title>
            <slot:content>
                <x-empty-state icon="lucide-calendar-off" title="Shut for {{ $closure->title }}"
                    description="The calendar closes the school on {{ \Illuminate\Support\Carbon::parse($attendedOn)->format('j F Y') }}, so that day has no register." />
            </slot:content>
        </april:card>
    @elseif ($students->isEmpty())
        <april:card>
            <slot:title>{{ $section->academicLevel?->name }} · {{ $section->label ?? $section->name }}</slot:title>
            <slot:content>
                <x-empty-state icon="lucide-users" title="Nobody attends this {{ strtolower(school_term('section', 'section')) }} yet"
                    description="Place a learner here first, then the register will list them.">
                    <april:button-link href="{{ route('students.index') }}">Go to students</april:button-link>
                </x-empty-state>
            </slot:content>
        </april:card>
    @else
        @include('livewire.partials.attendance-register-form')
    @endif
</div>
