@props([
    'items' => [],
])

@php
    $items = array_values(array_filter($items));
@endphp

<div class="flex items-center justify-end">
    @if (count($items) === 1)
        @php($item = $items[0])
        @if (($item['type'] ?? 'link') === 'delete')
            <form method="POST" x-bind:action="row.{{ $item['url'] }}" onsubmit="return confirm(@js($item['confirm'] ?? 'Delete this item?'))">
                @csrf
                @method('DELETE')
                <april:button type="submit" variant="outline" size="sm">
                    <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="size-4" />
                    <span class="sr-only">{{ $item['label'] }}</span>
                </april:button>
            </form>
        @else
            <april:button-link x-bind:href="row.{{ $item['url'] }}" variant="outline" size="sm">
                <x-icon :name="'lucide-'.($item['icon'] ?? 'arrow-up-right')" class="size-4" />
                <span class="sr-only">{{ $item['label'] }}</span>
            </april:button-link>
        @endif
    @elseif (count($items) > 1)
        <april:dropdown-menu x-teleport="body">
            <slot:trigger>
                <april:button type="button" variant="outline" size="sm" aria-label="Row actions">
                    <x-lucide-ellipsis class="size-4" />
                    <span class="sr-only">Row actions</span>
                </april:button>
            </slot:trigger>
            <slot:content align="end" class="w-52">
                @foreach ($items as $item)
                    @if (($item['type'] ?? 'link') === 'delete')
                        <form method="POST" x-bind:action="row.{{ $item['url'] }}" class="hidden" x-bind:id="'table-action-'+row.id+'-{{ $loop->index }}'">
                            @csrf
                            @method('DELETE')
                        </form>
                        <april:dropdown-menu-item class="text-destructive" x-on:click="document.getElementById('table-action-'+row.id+'-{{ $loop->index }}').requestSubmit()">
                            <x-icon :name="'lucide-'.($item['icon'] ?? 'trash-2')" class="mr-2 size-4" />
                            <span>{{ $item['label'] }}</span>
                        </april:dropdown-menu-item>
                    @else
                        <april:dropdown-menu-item x-on:click="window.location.href = row.{{ $item['url'] }}">
                            <x-icon :name="'lucide-'.($item['icon'] ?? 'arrow-up-right')" class="mr-2 size-4" />
                            <span>{{ $item['label'] }}</span>
                        </april:dropdown-menu-item>
                    @endif
                @endforeach
            </slot:content>
        </april:dropdown-menu>
    @endif
</div>
