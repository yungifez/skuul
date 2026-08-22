@props(['timetable'])

@php
    use App\Enums\TimetableStatus;

    $status = $timetable->status;
    $variant = match ($status) {
        TimetableStatus::Draft => 'secondary',
        TimetableStatus::Published => 'default',
        TimetableStatus::Archived => 'outline',
    };
    $periodAcceptsChanges = $timetable->academicPeriod?->isOpen()
        && $timetable->academicPeriod?->academicYear?->isOpen();
@endphp

<div class="flex flex-wrap items-center gap-2">
    <april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>

    @if ($status === TimetableStatus::Draft && $periodAcceptsChanges && auth()->user()->can('publish', $timetable))
        <form action="{{ route('timetables.publish', $timetable) }}" method="POST" onsubmit="return confirm('Publish this timetable? Published entries cannot be edited.')">
            @csrf
            <april:button type="submit" size="sm">
                <x-lucide-send class="mr-1.5 size-3.5" />
                Publish
            </april:button>
        </form>
    @elseif ($status === TimetableStatus::Published && $periodAcceptsChanges && auth()->user()->can('revise', $timetable))
        <form action="{{ route('timetables.revise', $timetable) }}" method="POST">
            @csrf
            <april:button type="submit" variant="outline" size="sm">
                <x-lucide-copy-plus class="mr-1.5 size-3.5" />
                New revision
            </april:button>
        </form>
    @endif

    @if (!$periodAcceptsChanges && $status !== TimetableStatus::Archived)
        <span class="text-xs text-muted-foreground">Period closed</span>
    @endif
</div>
