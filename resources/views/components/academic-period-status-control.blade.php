@props(['period', 'routePrefix'])

@php
    use App\Enums\AcademicPeriodStatus;

    $status = $period->status;
    $variant = match ($status) {
        AcademicPeriodStatus::Draft => 'secondary',
        AcademicPeriodStatus::Scheduled => 'secondary',
        AcademicPeriodStatus::Open => 'default',
        AcademicPeriodStatus::Closing => 'outline',
        AcademicPeriodStatus::Closed => 'outline',
        AcademicPeriodStatus::Archived => 'outline',
    };
    $canClose = auth()->user()->can('close', $period);
    $canReopen = auth()->user()->can('reopen', $period);
@endphp

<div class="flex flex-wrap items-center gap-2">
    <april:badge variant="{{ $variant }}">{{ $status->label() }}</april:badge>

    @if ($status === AcademicPeriodStatus::Open && $canClose)
        <form action="{{ route($routePrefix.'.begin-closing', $period) }}" method="POST">
            @csrf
            <april:button type="submit" variant="outline" size="sm">
                <x-lucide-lock class="mr-1.5 size-3.5" />
                Start closing
            </april:button>
        </form>
    @endif

    @if ($status === AcademicPeriodStatus::Closing && $canClose)
        <details class="rounded-md border p-2">
            <summary class="cursor-pointer text-sm font-medium">Confirm close</summary>
            <form action="{{ route($routePrefix.'.close', $period) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2">
                @csrf
                <div class="flex flex-col gap-1"><label for="close-reason-{{ $period->id }}" class="text-xs">Closure note</label><input id="close-reason-{{ $period->id }}" name="reason" class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" maxlength="500"></div>
                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="force" value="1">Close despite blocking checklist findings</label>
                <april:button type="submit" size="sm">Confirm close</april:button>
            </form>
        </details>
    @endif

    @if ($status === AcademicPeriodStatus::Closed && $canReopen)
        <details class="rounded-md border p-2">
            <summary class="cursor-pointer text-sm font-medium">Reopen</summary>
            <form action="{{ route($routePrefix.'.reopen', $period) }}" method="POST" class="mt-3 flex flex-wrap items-end gap-2">
                @csrf
                <div class="flex flex-col gap-1"><label for="reopen-reason-{{ $period->id }}" class="text-xs">Reason *</label><input id="reopen-reason-{{ $period->id }}" name="reason" required class="rounded-md border border-input bg-background px-2 py-1.5 text-sm" maxlength="500"></div>
                <april:button type="submit" variant="outline" size="sm"><x-lucide-lock-open class="mr-1.5 size-3.5" />Reopen</april:button>
            </form>
        </details>
    @endif
</div>
