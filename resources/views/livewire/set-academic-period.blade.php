@if ($academicYear !== null)
    @php
        $periodLabel = strtolower(school_term('period', 'term'));
        $workingPeriodDiffers = $currentPeriod?->id !== null && $workingPeriod?->id !== $currentPeriod->id;
    @endphp

    @if ($compact)
        <div class="border-b bg-muted/20 px-4 py-2 md:px-6">
            <div class="mx-auto flex max-w-screen-2xl flex-col gap-2 text-xs sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-muted-foreground">Current {{ $periodLabel }}</span>
                        <april:badge variant="{{ $currentPeriod === null ? 'outline' : 'secondary' }}">{{ $currentPeriod?->displayName ?? 'No term today' }}</april:badge>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="text-muted-foreground">Working {{ $periodLabel }}</span>
                        <april:badge variant="{{ $workingPeriodDiffers ? 'default' : 'secondary' }}">{{ $workingPeriod?->displayName ?? 'Not selected' }}</april:badge>
                    </span>
                    @if ($workingPeriodDiffers)
                        <span class="text-amber-700 dark:text-amber-300">You are viewing {{ $workingPeriod->displayName }}.</span>
                    @endif
                </div>
                @if ($this->canChange() && $academicPeriods->isNotEmpty())
                    <form action="{{ route('academic-periods.set-academic-period') }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <label for="compact-working-period" class="sr-only">Set working {{ $periodLabel }}</label>
                        <select name="academic_period_id" id="compact-working-period" class="h-8 rounded-md border bg-background px-2 text-xs" aria-label="Set working {{ $periodLabel }}">
                            @foreach ($academicPeriods as $academicPeriod)
                                <option value="{{ $academicPeriod->id }}" @selected($workingPeriod?->id === $academicPeriod->id)>{{ $academicPeriod->displayName }}</option>
                            @endforeach
                        </select>
                        <april:button size="sm" type="submit">Set working {{ $periodLabel }}</april:button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <form action="{{ route('academic-periods.set-academic-period') }}" method="POST" class="card-body">
                <x-display-validation-errors />
                <div class="flex w-full flex-col gap-2">
                    <april:label for="set-academic-period-form">Set working {{ $periodLabel }}</april:label>
                    <p class="text-sm text-muted-foreground">
                        Current {{ $periodLabel }}: <span class="font-medium text-foreground">{{ $currentPeriod?->displayName ?? 'No term covers today' }}</span>.
                        Working {{ $periodLabel }}: <span class="font-medium text-foreground">{{ $workingPeriod?->displayName ?? 'Not selected' }}</span>.
                        This choice is saved for you in this school and {{ strtolower(school_term('academic_year', 'school year')) }}.
                    </p>
                    <april:select name="academic_period_id" id="set-academic-period-form">
                        @foreach ($academicPeriods as $academicPeriod)
                            <option value="{{ $academicPeriod->id }}" @selected($workingPeriod?->id === $academicPeriod->id)>{{ $academicPeriod->displayName }}</option>
                        @endforeach
                    </april:select>
                    @error('academic_period_id')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                @csrf
                <div class="mt-6 flex items-center justify-end">
                    <april:button type="submit">
                        <x-lucide-check class="mr-2 size-4" />
                        Save working {{ $periodLabel }}
                    </april:button>
                </div>
            </form>
        </div>
    @endif
@endif
