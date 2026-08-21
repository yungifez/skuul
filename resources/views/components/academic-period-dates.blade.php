@props(['period'])

@if ($period->starts_on !== null && $period->ends_on !== null)
    <span>{{ $period->starts_on->format('M j, Y') }} – {{ $period->ends_on->format('M j, Y') }}</span>
@else
    <span class="text-sm text-muted-foreground">Not scheduled</span>
@endif
