<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    @if ($canOverride && $overrideSections->isNotEmpty())
        <section class="flex flex-col gap-3" aria-labelledby="override-heading">
            <h2 id="override-heading" class="text-base font-semibold">Give a section its own version</h2>
            <p class="text-sm text-muted-foreground">The draft starts as a copy of this timetable. This one stays as it is.</p>
            <form wire:submit="startOverride" class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end" aria-label="Start a section's own version">
                <div>
                    <label for="section_id" class="text-sm text-muted-foreground">Section</label>
                    <select id="section_id" wire:model="sectionId" required class="{{ $controlClasses }}" {{ field_error_bindings('sectionId') }}>
                        <option value="">Choose a section</option>
                        @foreach ($overrideSections as $section)
                            <option value="{{ $section->id }}">{{ $section->qualifiedName() }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="sectionId" class="mt-1" />
                </div>
                <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="startOverride">Start the draft</april:button>
            </form>
        </section>
    @endif

    @if ($canCover && $lessons->isNotEmpty())
        <section class="flex flex-col gap-3" aria-labelledby="cover-heading">
            <h2 id="cover-heading" class="text-base font-semibold">Cover a lesson</h2>
            <p class="text-sm text-muted-foreground">Records who covers one date. The weekly timetable stays as published.</p>
            @if ($teachers->isEmpty())
                <p class="text-sm text-muted-foreground">Add an active teacher to this school before recording cover.</p>
            @else
                <form wire:submit="recordCover" class="flex flex-col gap-3" aria-label="Record cover">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="lesson" class="text-sm text-muted-foreground">Scheduled lesson</label>
                            <select id="lesson" wire:model="lesson" required class="{{ $controlClasses }}" {{ field_error_bindings('lesson') }}>
                                <option value="">Choose a lesson</option>
                                @foreach ($lessons as $entry)
                                    <option value="{{ $entry->timetable_time_slot_id }}:{{ $entry->weekday_id }}">{{ $entry->weekday_name }} · {{ $entry->start_time }}–{{ $entry->stop_time }}</option>
                                @endforeach
                            </select>
                            <x-field-error name="lesson" class="mt-1" />
                        </div>
                        <div>
                            <label for="teacher_id" class="text-sm text-muted-foreground">Covering teacher</label>
                            <select id="teacher_id" wire:model="teacherId" required class="{{ $controlClasses }}" {{ field_error_bindings('teacherId') }}>
                                <option value="">Choose a teacher</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            <x-field-error name="teacherId" class="mt-1" />
                        </div>
                        <div>
                            <label for="cover_date" class="text-sm text-muted-foreground">Date</label>
                            <input type="date" id="cover_date" wire:model="coverDate" required class="{{ $controlClasses }}" {{ field_error_bindings('coverDate') }}>
                            <x-field-error name="coverDate" class="mt-1" />
                        </div>
                        <div>
                            <label for="reason" class="text-sm text-muted-foreground">Reason</label>
                            <input id="reason" wire:model="reason" required maxlength="1000" placeholder="Staff absence" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                            <x-field-error name="reason" class="mt-1" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="recordCover">Record cover</april:button>
                    </div>
                </form>
            @endif
        </section>
    @endif

    @if ($substitutions->isNotEmpty())
        <section class="flex flex-col gap-3" aria-labelledby="covered-heading">
            <h2 id="covered-heading" class="text-base font-semibold">Recorded cover</h2>
            <ul class="divide-y border-y">
                @foreach ($substitutions as $substitution)
                    <li wire:key="cover-{{ $substitution->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">
                                {{ $substitution->substituted_on->format('D j M Y') }} · {{ $substitution->timeSlot?->start_time ?? '—' }}–{{ $substitution->timeSlot?->stop_time ?? '—' }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $substitution->replacementTeacher?->name ?? '—' }} covers · {{ $substitution->reason }} · approved by {{ $substitution->approvedBy?->name ?? '—' }}
                            </p>
                        </div>
                        @if ($canWithdraw && !$substitution->substituted_on->lt(school_today()))
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for the cover on {{ $substitution->substituted_on->format('j M') }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="withdrawCover({{ $substitution->id }})" wire:confirm="Withdraw this cover? The lesson goes back to its usual teacher."><x-lucide-undo-2 class="mr-2 size-4" />Withdraw</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
