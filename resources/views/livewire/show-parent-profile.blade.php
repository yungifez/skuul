<div class="flex flex-col gap-10">
    <livewire:show-user-profile :user="$parent" />

    <section aria-labelledby="linked-learners-heading" class="flex flex-col gap-3">
        <div class="flex items-center justify-between gap-3">
            <h2 id="linked-learners-heading" class="text-base font-semibold">Linked learners</h2>
            @can('update', [$parent, 'parent'])
                <april:button-link href="{{ route('parents.assign-student', $parent) }}" variant="outline" class="h-11 select-none">Change linked learners</april:button-link>
            @endcan
        </div>

        @if ($children->isEmpty())
            <p class="border-y py-4 text-sm text-muted-foreground">No learner at this school is linked yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($children as $student)
                    @php
                        $cycleSection = $student->studentRecord?->academicCycleSection;
                    @endphp
                    <li wire:key="child-{{ $student->id }}" class="flex min-h-11 flex-col justify-center py-3">
                        @can('view', [$student, 'student'])
                            <a href="{{ route('students.show', $student) }}" class="truncate font-medium underline-offset-4 hover:underline">{{ $student->name }}</a>
                        @else
                            <p class="truncate font-medium">{{ $student->name }}</p>
                        @endcan
                        <p class="truncate text-sm text-muted-foreground">{{ $cycleSection === null ? 'Not placed' : $cycleSection->academicLevel->name.' · '.($cycleSection->label ?? $cycleSection->name) }} · {{ $student->studentRecord?->admission_number ?? '—' }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
