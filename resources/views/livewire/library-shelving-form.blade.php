<div>
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    @if (!$isShelving)
        <april:button type="button" variant="outline" class="h-11 select-none" wire:click="start">
            <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Put a book on the shelf
        </april:button>
    @else
        <form wire:submit="save" class="flex flex-col gap-4 border-y py-4" aria-label="Put a book on the shelf">
            @if ($picked !== null)
                <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ $picked->title }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $picked->authors ?? '—' }}</p>
                    </div>
                    <april:button type="button" variant="ghost" class="h-11 shrink-0 select-none" wire:click="forgetTitle">Change</april:button>
                </div>
            @elseif ($isDescribing)
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="shelve-title" class="text-sm text-muted-foreground">Title</label>
                        <input id="shelve-title" wire:model="title" required maxlength="255" class="{{ $controlClasses }}" {{ field_error_bindings('title') }}>
                        <x-field-error name="title" class="mt-1" />
                    </div>
                    <div>
                        <label for="shelve-authors" class="text-sm text-muted-foreground">Author (optional)</label>
                        <input id="shelve-authors" wire:model="authors" maxlength="255" class="{{ $controlClasses }}" {{ field_error_bindings('authors') }}>
                        <x-field-error name="authors" class="mt-1" />
                    </div>
                    <div>
                        <label for="shelve-isbn" class="text-sm text-muted-foreground">ISBN (optional)</label>
                        <input id="shelve-isbn" wire:model="isbn" maxlength="20" class="{{ $controlClasses }}" {{ field_error_bindings('isbn') }}>
                        <x-field-error name="isbn" class="mt-1" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="shelve-category" class="text-sm text-muted-foreground">Category (optional)</label>
                        <input id="shelve-category" wire:model="category" maxlength="80" class="{{ $controlClasses }}" {{ field_error_bindings('category') }}>
                        <x-field-error name="category" class="mt-1" />
                    </div>
                </div>
            @else
                <div>
                    <label for="shelve-search" class="text-sm text-muted-foreground">Which book</label>
                    <input id="shelve-search" type="search" wire:model.live.debounce.300ms="titleSearch" autocomplete="off" placeholder="Title, author or ISBN" class="{{ $controlClasses }}" {{ field_error_bindings('titleSearch') }}>
                    <x-field-error name="titleSearch" class="mt-1" />
                </div>
                @if ($matches->isNotEmpty())
                    <ul class="divide-y border-y" aria-label="Books in the catalogue">
                        @foreach ($matches as $match)
                            <li wire:key="shelve-title-{{ $match->id }}">
                                <button type="button" wire:click="pickTitle({{ $match->id }})" class="flex min-h-11 w-full select-none flex-col items-start justify-center py-2 text-left hover:bg-muted">
                                    <span class="w-full truncate text-sm">{{ $match->title }}</span>
                                    <span class="w-full truncate text-xs text-muted-foreground">{{ $match->authors ?? '—' }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" wire:click="describeNew" class="min-h-11 select-none self-start text-sm font-medium underline-offset-4 hover:underline">
                    {{ mb_strlen(trim($titleSearch)) >= 2 && $matches->isEmpty() ? 'Not in the catalogue. Describe it' : 'Describe a new book' }}
                </button>
            @endif

            <div class="grid gap-3 sm:grid-cols-3">
                <div>
                    <label for="shelve-barcode" class="text-sm text-muted-foreground">Barcode of the first copy</label>
                    <input id="shelve-barcode" wire:model.blur="barcode" required maxlength="60" autocomplete="off" class="{{ $controlClasses }} font-mono" {{ field_error_bindings('barcode') }}>
                </div>
                <div>
                    <label for="shelve-copies" class="text-sm text-muted-foreground">How many copies</label>
                    <input id="shelve-copies" type="number" inputmode="numeric" min="1" max="50" required wire:model.blur="copies" class="{{ $controlClasses }}" {{ field_error_bindings('copies') }}>
                </div>
                <div>
                    <label for="shelve-shelf-mark" class="text-sm text-muted-foreground">Shelf mark (optional)</label>
                    <input id="shelve-shelf-mark" wire:model="shelfMark" maxlength="60" class="{{ $controlClasses }}" {{ field_error_bindings('shelfMark') }}>
                </div>
            </div>
            <x-field-error name="barcode" />
            <x-field-error name="copies" />
            <x-field-error name="shelfMark" />
            @if ((int) $copies > 1 && trim($barcode) !== '')
                <p class="text-xs text-muted-foreground">Barcodes {{ trim($barcode) }}, {{ trim($barcode) }}-1 … {{ trim($barcode) }}-{{ (int) $copies - 1 }}</p>
            @endif

            <div class="flex justify-end gap-2">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stop">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Add to the shelf</april:button>
            </div>
        </form>
    @endif
</div>
