{{-- What one cell of the week holds. --}}
@if ($cell['kind'] === null)
    <span class="text-xs text-muted-foreground">&mdash;</span>
@else
    <span class="block text-xs font-medium leading-snug">{{ $cell['name'] }}</span>

    @if ($cell['teachers'] !== [])
        <span class="mt-1 block text-[0.7rem] leading-snug text-muted-foreground">
            {{ implode(', ', $cell['teachers']) }}
        </span>
    @elseif ($cell['kind'] === 'break')
        <span class="mt-1 block text-[0.7rem] leading-snug text-muted-foreground">No lesson</span>
    @endif
@endif
