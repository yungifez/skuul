<section class="flex flex-col gap-4" aria-labelledby="house-checks-heading">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <h2 id="house-checks-heading" class="text-base font-semibold">{{ $day->format('l, j F Y') }}</h2>
        <div class="w-full sm:w-48">
            <label for="roll-date" class="sr-only">Day</label>
            <input id="roll-date" type="date" wire:model.live="date" value="{{ $day->toDateString() }}" max="{{ school_today()->toDateString() }}" class="h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
        </div>
    </div>

    @if ($houses->isEmpty())
        <p class="text-sm text-muted-foreground">No open boarding houses</p>
    @else
        <ul class="divide-y border-y">
            @foreach ($houses as $house)
                <li wire:key="house-{{ $house->id }}" class="flex flex-col gap-2 py-3 md:flex-row md:items-start md:gap-4">
                    <p class="min-w-0 break-words text-sm font-medium md:w-48 md:pt-3">{{ $house->name }}</p>
                    <ul class="grid flex-1 gap-2 sm:grid-cols-3" aria-label="Rolls for {{ $house->name }}">
                        @foreach ($types as $type)
                            @php($roll = $rolls->get($house->id, collect())->firstWhere('type', $type))
                            <li wire:key="house-{{ $house->id }}-{{ $type->value }}">
                                @if ($roll)
                                    <a href="{{ route('boarding-rolls.show', $roll) }}" class="flex min-h-11 select-none flex-col justify-center rounded-md border px-3 py-2 hover:bg-muted">
                                        <span class="text-sm font-medium">{{ $type->label() }}</span>
                                        <span class="text-xs text-muted-foreground">
                                            {{ $roll->isComplete() ? 'Complete' : ($roll->entries_count - $roll->unanswered_count).' of '.$roll->entries_count.' answered' }}
                                        </span>
                                    </a>
                                @elseif ($canManage && !$isFuture)
                                    <button type="button" wire:click="start({{ $house->id }}, '{{ $type->value }}')" wire:loading.attr="disabled" wire:target="start" aria-label="Start the {{ strtolower($type->label()) }} for {{ $house->name }}" class="flex min-h-11 w-full select-none flex-col justify-center rounded-md border border-dashed px-3 py-2 text-left hover:bg-muted">
                                        <span class="text-sm font-medium">{{ $type->label() }}</span>
                                        <span class="text-xs text-muted-foreground">Start</span>
                                    </button>
                                @else
                                    <div class="flex min-h-11 flex-col justify-center rounded-md border border-dashed px-3 py-2">
                                        <span class="text-sm">{{ $type->label() }}</span>
                                        <span class="text-xs text-muted-foreground">Not started</span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endif
</section>
