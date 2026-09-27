@php
    $periodLabel = strtolower(school_term('period', 'term'));
    $workingPeriodDiffers = $currentPeriod?->id !== null && $workingPeriod?->id !== $currentPeriod->id;
    $canPick = $this->canChange() && $academicPeriods->isNotEmpty() && !$calendarError;
@endphp

{{-- Livewire reads the first tag in this view as the component root. A
     directive before it writes a comment marker instead, and the next
     request from a parent component then fails on an empty tag name.
     Keep this element unconditional. --}}
<div @class([
    'border-b px-4 py-1.5 md:px-6' => $academicYear !== null && $compact,
    'min-w-0' => $academicYear !== null && !$compact,
])>
    @if ($academicYear !== null)
        <div @class([
            'flex flex-wrap items-center gap-x-4 gap-y-1',
            'mx-auto max-w-screen-2xl text-xs' => $compact,
            'text-sm' => !$compact,
        ])>
            @php
                $selectClasses = [
                    'rounded-md border border-input bg-background px-2 font-medium disabled:opacity-60',
                    'h-11 text-xs sm:h-8' => $compact,
                    'h-11 text-sm' => !$compact,
                ];
                $yearOptions = $academicYears->contains('id', $academicYear->id) ? $academicYears : $academicYears->prepend($academicYear);
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-muted-foreground">Working</span>
                @if ($compact && $this->canChangeYear() && $yearOptions->count() > 1)
                    <label for="working-year" class="sr-only">Working {{ strtolower(school_term('academic_year', 'school year')) }}</label>
                    <select id="working-year" wire:model.live="workingYearId" wire:loading.attr="disabled" @class($selectClasses) {{ field_error_bindings('workingYearId') }}>
                        @foreach ($yearOptions as $yearOption)
                            <option value="{{ $yearOption->id }}">{{ $yearOption->name }}</option>
                        @endforeach
                    </select>
                @elseif ($compact)
                    <span class="font-medium">{{ $academicYear->name }}</span>
                @endif
                @if ($canPick)
                    <label for="working-period-{{ $compact ? 'bar' : 'page' }}" class="sr-only">Working {{ $periodLabel }}</label>
                    <select id="working-period-{{ $compact ? 'bar' : 'page' }}" wire:model.live="workingPeriodId" wire:loading.attr="disabled" @class($selectClasses) {{ field_error_bindings('workingPeriodId') }}>
                        @foreach ($academicPeriods as $academicPeriod)
                            <option value="{{ $academicPeriod->id }}">{{ $academicPeriod->displayName }}</option>
                        @endforeach
                    </select>
                @else
                    <span class="font-medium">{{ $workingPeriod?->displayName ?? 'No '.$periodLabel }}</span>
                @endif
            </div>

            @if ($calendarError)
                <span class="inline-flex items-center gap-1.5 text-destructive" role="alert">
                    <x-lucide-triangle-alert class="size-3.5 shrink-0" />
                    {{ $calendarError }}
                </span>
            @elseif ($workingPeriodDiffers)
                <span class="inline-flex items-center gap-1.5 text-amber-700 dark:text-amber-300">
                    <span class="size-1.5 rounded-full bg-current"></span>
                    Today is {{ $currentPeriod->displayName }}
                </span>
            @elseif ($currentPeriod === null)
                <span class="text-muted-foreground">No {{ $periodLabel }} today</span>
            @endif

            <x-field-error name="workingYearId" />
            <x-field-error name="workingPeriodId" />
        </div>
    @endif
</div>
