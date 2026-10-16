<form wire:submit="save" class="grid gap-4 lg:grid-cols-4 lg:items-end" aria-label="Import a file">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <div>
        <label for="import-type" class="text-sm text-muted-foreground">What the file holds</label>
        <select id="import-type" wire:model="type" required class="{{ $controlClasses }}" {{ field_error_bindings('type') }}>
            <option value="">Choose an import</option>
            @foreach ($imports as $import)
                <option value="{{ $import['key'] }}">{{ $import['title'] }}</option>
            @endforeach
        </select>
        <x-field-error name="type" class="mt-1" />
    </div>

    <div class="lg:col-span-2">
        <label for="import-file" class="text-sm text-muted-foreground">CSV file</label>
        <input type="file" id="import-file" wire:model="file" accept=".csv,text/csv" required
            class="{{ $controlClasses }} py-2 file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium" {{ field_error_bindings('file') }}>
        <p class="mt-1 text-xs text-muted-foreground">Up to 5 MB. The first line must name the columns.</p>
        <x-field-error name="file" class="mt-1" />
    </div>

    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save,file">
        <x-lucide-upload class="mr-2 size-4" />
        Check the file
    </april:button>
</form>
