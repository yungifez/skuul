@props([
    'items' => [],
])

@php
    $items = array_values(array_filter($items));

    // An item may carry a `when` key holding an Alpine expression over `row`.
    // The control then shows only on the rows it works on, so no screen
    // offers an action the server will refuse. The class is worked out here
    // because a Blade directive inside an april tag breaks its precompiler.
    $shownWhen = static fn (array $item): string => isset($item['when'])
        ? '('.$item['when'].') ? \'\' : \'hidden\''
        : '\'\'';

    // "Delete this student?" names nobody on a table of twenty-five rows. An
    // item may carry a `names` key holding an Alpine expression over `row`,
    // and `:name` in its message is replaced with what that expression reads.
    $confirmBinding = static function (array $item): ?string {
        if (! isset($item['names'])) {
            return null;
        }

        $parts = explode(':name', $item['confirm'] ?? 'Delete :name?', 2);

        return json_encode($parts[0]).' + ('.$item['names'].') + '.json_encode($parts[1] ?? '');
    };

    // An `action` item calls a method of the table's Livewire component with
    // the row id, after the browser's confirm when the item names one.
    $actionCall = static function (array $item) use ($confirmBinding): string {
        $call = '$wire.call('.json_encode($item['method']).', row.id)';

        if (! isset($item['confirm']) && ! isset($item['names'])) {
            return $call;
        }

        $message = $confirmBinding($item) ?? json_encode($item['confirm']);

        return 'window.confirm('.$message.') && '.$call;
    };
@endphp

<div class="flex items-center justify-end">
    @if (count($items) === 1)
        @php($item = $items[0])
        @if (($item['type'] ?? 'link') === 'action')
            <april:button type="button" variant="outline" size="icon" class="size-11 select-none" x-on:click="{{ $actionCall($item) }}" x-bind:class="{{ $shownWhen($item) }}" x-bind:aria-label="{{ isset($item['names']) ? json_encode($item['label'].' ').' + ('.$item['names'].')' : json_encode($item['label']) }}">
                <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="size-4" />
            </april:button>
        @elseif (($item['type'] ?? 'link') === 'delete')
            <form method="POST" x-bind:action="row.{{ $item['url'] }}"
                @if ($confirmBinding($item) !== null) x-bind:data-confirm="{{ $confirmBinding($item) }}" @else data-confirm="{{ $item['confirm'] ?? 'Delete this item?' }}" @endif
                x-bind:class="{{ $shownWhen($item) }}">
                @csrf
                @method('DELETE')
                <april:button type="submit" variant="outline" size="icon" class="size-11 select-none">
                    <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="size-4" />
                    <span class="sr-only">{{ $item['label'] }}</span>
                </april:button>
            </form>
        @else
            <april:button-link x-bind:href="row.{{ $item['url'] }}" variant="outline" size="icon" class="size-11 select-none" x-bind:class="{{ $shownWhen($item) }}">
                <x-icon :name="'lucide-'.($item['icon'] ?? 'arrow-up-right')" class="size-4" />
                <span class="sr-only">{{ $item['label'] }}</span>
            </april:button-link>
        @endif
    @elseif (count($items) > 1)
        <april:dropdown-menu x-teleport="body">
            <slot:trigger>
                <april:button type="button" variant="outline" size="icon" class="size-11 select-none" aria-label="Row actions">
                    <x-lucide-ellipsis class="size-4" />
                    <span class="sr-only">Row actions</span>
                </april:button>
            </slot:trigger>
            <slot:content align="end" class="w-52">
                @foreach ($items as $item)
                    @if (($item['type'] ?? 'link') === 'action')
                        <april:dropdown-menu-item class="{{ ($item['destructive'] ?? true) ? 'text-destructive' : '' }}" x-bind:class="{{ $shownWhen($item) }}" x-on:click="{{ $actionCall($item) }}">
                            <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="mr-2 size-4" />
                            <span>{{ $item['label'] }}</span>
                        </april:dropdown-menu-item>
                    @elseif (($item['type'] ?? 'link') === 'delete')
                        <form method="POST" x-bind:action="row.{{ $item['url'] }}"
                            @if ($confirmBinding($item) !== null) x-bind:data-confirm="{{ $confirmBinding($item) }}" @else data-confirm="{{ $item['confirm'] ?? 'Delete this item?' }}" @endif
                            class="hidden" x-bind:id="'table-action-'+row.id+'-{{ $loop->index }}'">
                            @csrf
                            @method('DELETE')
                        </form>
                        <april:dropdown-menu-item class="text-destructive" x-bind:class="{{ $shownWhen($item) }}" x-on:click="document.getElementById('table-action-'+row.id+'-{{ $loop->index }}').requestSubmit()">
                            <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="mr-2 size-4" />
                            <span>{{ $item['label'] }}</span>
                        </april:dropdown-menu-item>
                    @else
                        <april:dropdown-menu-item x-bind:class="{{ $shownWhen($item) }}" x-on:click="window.location.href = row.{{ $item['url'] }}">
                            <x-icon :name="'lucide-'.($item['icon'] ?? 'arrow-up-right')" class="mr-2 size-4" />
                            <span>{{ $item['label'] }}</span>
                        </april:dropdown-menu-item>
                    @endif
                @endforeach
            </slot:content>
        </april:dropdown-menu>
    @endif
</div>
