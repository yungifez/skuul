<div class="space-y-6">
    <livewire:show-user-profile :user="$student" />

    @if ($studentRecord)
        <april:card>
            <slot:title class="flex flex-wrap items-center justify-between gap-3">
                <span>Enrollment</span>
                <x-enrollment-status :enrollment="$studentRecord" />
            </slot:title>
            <slot:description>
                This is the student’s record in the current school. Account access and enrollment status are separate.
            </slot:description>
            <slot:content class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Admission number</p>
                        <p class="mt-1 font-semibold">{{ $studentRecord->admission_number ?: 'Not assigned' }}</p>
                    </div>
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Admitted</p>
                        <p class="mt-1 font-semibold">{{ $studentRecord->admission_date ?: 'Not recorded' }}</p>
                    </div>
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Current class</p>
                        <p class="mt-1 font-semibold">{{ $studentRecord->myClass?->name ?: 'Not placed' }}</p>
                    </div>
                    <div class="rounded-lg border bg-muted/30 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Current section</p>
                        <p class="mt-1 font-semibold">{{ $studentRecord->section?->name ?: 'Not placed' }}</p>
                    </div>
                </div>

                @if ($canManageEnrollment)
                    <div class="grid gap-6 border-t pt-6 lg:grid-cols-2">
                        <form wire:submit="changeStatus" class="space-y-4">
                            <div>
                                <h3 class="font-semibold">Change enrollment status</h3>
                                <p class="text-sm text-muted-foreground">Every change is recorded with the actor, date, and reason.</p>
                            </div>

                            <div class="flex flex-col gap-2">
                                <april:label for="status-selection">New status</april:label>
                                <april:select id="status-selection" wire:model.live="statusSelection">
                                    @foreach ($statusOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </april:select>
                                @error('statusSelection')
                                    <p class="text-sm text-destructive">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <april:input-group id="status-effective-on" type="date" label="Effective on" wire:model.live="statusEffectiveOn" />
                                <div class="flex flex-col gap-2">
                                    <april:label for="status-reason">Reason</april:label>
                                    <april:textarea id="status-reason" wire:model.live="statusReason" rows="2" placeholder="Optional reason" />
                                </div>
                            </div>

                            <april:button type="submit" wire:loading.attr="disabled" wire:target="changeStatus">
                                <x-lucide-refresh-cw class="mr-2 size-4" />
                                Save status
                            </april:button>
                        </form>

                        <form wire:submit="changePlacement" class="space-y-4">
                            <div>
                                <h3 class="font-semibold">Change placement</h3>
                                <p class="text-sm text-muted-foreground">A new placement keeps the previous class and section in history.</p>
                            </div>

                            @if ($academicYear)
                                <p class="rounded-md border bg-muted/30 px-3 py-2 text-sm">
                                    Working academic year: <span class="font-medium">{{ $academicYear->name }}</span>
                                    @if ($academicPeriod)
                                        <span class="text-muted-foreground">· {{ $academicPeriod->name }}</span>
                                    @endif
                                </p>
                            @else
                                <april:alert variant="destructive">
                                    <slot:title>No academic year selected</slot:title>
                                    <slot:description>Select a working academic year before changing placement.</slot:description>
                                </april:alert>
                            @endif

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="flex flex-col gap-2">
                                    <april:label for="placement-class">Class</april:label>
                                    <april:select id="placement-class" wire:model.live="placementClassId" @disabled(!$academicYear || $studentRecord->status->isClosed())>
                                        <option value="">Choose a class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                                        @endforeach
                                    </april:select>
                                    @error('placementClassId')
                                        <p class="text-sm text-destructive">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex flex-col gap-2">
                                    <april:label for="placement-section">Section</april:label>
                                    <april:select id="placement-section" wire:model.live="placementSectionId" @disabled(!$placementClassId || !$academicYear || $studentRecord->status->isClosed())>
                                        <option value="">No section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section['id'] }}">{{ $section['name'] }}</option>
                                        @endforeach
                                    </april:select>
                                    @error('placementSectionId')
                                        <p class="text-sm text-destructive">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <april:input-group id="placement-effective-on" type="date" label="Effective on" wire:model.live="placementEffectiveOn" />
                                <div class="flex flex-col gap-2">
                                    <april:label for="placement-reason">Reason</april:label>
                                    <april:textarea id="placement-reason" wire:model.live="placementReason" rows="2" placeholder="Optional reason" />
                                </div>
                            </div>

                            <april:button type="submit" wire:loading.attr="disabled" wire:target="changePlacement" :disabled="!$academicYear || $studentRecord->status->isClosed()">
                                <x-lucide-arrow-right-left class="mr-2 size-4" />
                                Save placement
                            </april:button>
                        </form>
                    </div>
                @endif
            </slot:content>
        </april:card>

        <div class="grid gap-6 lg:grid-cols-2">
            <april:card>
                <slot:title>Status history</slot:title>
                <slot:description>Enrollment state is append-only. Older records remain visible.</slot:description>
                <slot:content>
                    @if ($studentRecord->statusChanges->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($studentRecord->statusChanges->sortByDesc('effective_on') as $change)
                                <div class="flex gap-3" wire:key="enrollment-status-change-{{ $change->id }}">
                                    <div class="mt-1 rounded-full bg-primary/10 p-2 text-primary">
                                        <x-lucide-history class="size-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <april:badge variant="outline">{{ $change->to_status->label() }}</april:badge>
                                            <span class="text-sm text-muted-foreground">{{ $change->effective_on?->format('M j, Y') }}</span>
                                        </div>
                                        <p class="mt-1 text-sm text-muted-foreground">
                                            From {{ $change->from_status->label() }}
                                            @if ($change->changedBy)
                                                · by {{ $change->changedBy->name }}
                                            @endif
                                        </p>
                                        @if ($change->reason)
                                            <p class="mt-1 text-sm">{{ $change->reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted-foreground">No status changes have been recorded.</p>
                    @endif
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Placement history</slot:title>
                <slot:description>Every class and section assignment for this enrollment.</slot:description>
                <slot:content>
                    @if ($studentRecord->placements->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b text-xs uppercase tracking-wide text-muted-foreground">
                                    <tr>
                                        <th class="px-2 py-3 font-medium">Period</th>
                                        <th class="px-2 py-3 font-medium">Placement</th>
                                        <th class="px-2 py-3 font-medium">Effective</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($studentRecord->placements->sortByDesc('effective_on') as $placement)
                                        <tr wire:key="enrollment-placement-{{ $placement->id }}">
                                            <td class="px-2 py-3">{{ $placement->academicYear?->name ?: '—' }}<span class="block text-xs text-muted-foreground">{{ $placement->academicPeriod?->name }}</span></td>
                                            <td class="px-2 py-3">{{ $placement->myClass?->name ?: '—' }}<span class="block text-xs text-muted-foreground">{{ $placement->section?->name ?: 'No section' }}</span></td>
                                            <td class="whitespace-nowrap px-2 py-3">{{ $placement->effective_on?->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-muted-foreground">No placement history has been recorded.</p>
                    @endif
                </slot:content>
            </april:card>
        </div>
    @else
        <april:alert variant="destructive">
            <slot:title>No enrollment in this school</slot:title>
            <slot:description>This person has a user account but no student enrollment attached to the current school.</slot:description>
        </april:alert>
    @endif
</div>
