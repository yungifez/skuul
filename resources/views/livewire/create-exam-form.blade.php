<form wire:submit="save" class="flex max-w-2xl flex-col gap-4" aria-label="Plan an exam">
    <p class="text-sm text-muted-foreground">
        The exam goes on the calendar of {{ $academicYear?->name ?? 'the chosen '.strtolower(school_term('academic_year', 'school year')) }} and stays with its reporting period.
    </p>

    @include('livewire.partials.exam-fields')

    <div class="flex justify-end">
        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save" :disabled="$academicPeriods->isEmpty()">
            Plan the exam
        </april:button>
    </div>
</form>
