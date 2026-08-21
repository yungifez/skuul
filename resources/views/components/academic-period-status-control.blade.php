@props(['period', 'routePrefix'])

@php
    use App\Enums\AcademicPeriodStatus;

    $status = $period->status;
    $variant = match ($status) {
        AcademicPeriodStatus::Draft => 'secondary',
        AcademicPeriodStatus::Open => 'default',
        AcademicPeriodStatus::Closed => 'outline',
    };
    $canClose = auth()->user()->can('close', $period);
    $canReopen = auth()->user()->can('reopen', $period);
@endphp

<div class="flex flex-wrap items-center gap-2">
    <april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>

    @if ($status !== AcademicPeriodStatus::Closed && $canClose)
        <form action="{{ route($routePrefix.'.close', $period) }}" method="POST" onsubmit="return confirm('Close this academic period? Its records will become read-only.')">
            @csrf
            <april:button type="submit" variant="outline" size="sm">
                <x-lucide-lock class="mr-1.5 size-3.5" />
                Close
            </april:button>
        </form>
    @elseif ($status === AcademicPeriodStatus::Closed && $canReopen)
        <form action="{{ route($routePrefix.'.reopen', $period) }}" method="POST" onsubmit="return confirm('Reopen this academic period for changes?')">
            @csrf
            <april:button type="submit" variant="outline" size="sm">
                <x-lucide-lock-open class="mr-1.5 size-3.5" />
                Reopen
            </april:button>
        </form>
    @endif
</div>
