@props(['organization', 'calendarTemplate' => null])

@php
    $storedPeriods = $calendarTemplate?->periods->values() ?? collect();
    $storedIndexes = $storedPeriods->pluck('id')->flip();
    $defaultPeriods = [
        ['name' => 'Term 1', 'label' => 'Term 1', 'type' => 'term', 'position' => 1, 'start_offset_days' => 0, 'length_days' => 84, 'parent_index' => null],
        ['name' => 'Term 2', 'label' => 'Term 2', 'type' => 'term', 'position' => 2, 'start_offset_days' => 112, 'length_days' => 84, 'parent_index' => null],
        ['name' => 'Term 3', 'label' => 'Term 3', 'type' => 'term', 'position' => 3, 'start_offset_days' => 224, 'length_days' => 84, 'parent_index' => null],
    ];
    $existingPeriods = $storedPeriods->map(fn ($period) => [
        'name' => $period->name,
        'label' => $period->label,
        'type' => $period->type->value,
        'position' => $period->position,
        'start_offset_days' => $period->start_offset_days,
        'length_days' => $period->length_days,
        'parent_index' => $period->parent_id ? (($storedIndexes[$period->parent_id] ?? -1) + 1) : null,
    ])->all();
    $periods = old('periods', $existingPeriods ?: $defaultPeriods);
    $periods = array_pad($periods, 8, ['name' => null, 'label' => null, 'type' => 'term', 'position' => null, 'start_offset_days' => null, 'length_days' => null, 'parent_index' => null]);
@endphp

<form method="POST" action="{{ $calendarTemplate ? route('organizations.calendar-templates.update', [$organization, $calendarTemplate]) : route('organizations.calendar-templates.store', $organization) }}" class="space-y-8">
    @csrf
    @if ($calendarTemplate)
        @method('PUT')
    @endif

    <x-display-validation-errors />

    <div class="grid gap-4 md:grid-cols-2">
        <april:input-group name="name" id="name" label="Template name *" value="{{ old('name', $calendarTemplate?->name) }}" required />
        <april:input-group name="cycle_length_days" id="cycle-length-days" type="number" min="1" max="3660" label="Cycle length in days *" value="{{ old('cycle_length_days', $calendarTemplate?->cycle_length_days ?? 365) }}" required />
        <div class="flex flex-col gap-2 md:col-span-2">
            <april:label for="description">Description</april:label>
            <april:textarea name="description" id="description" rows="3">{{ old('description', $calendarTemplate?->description) }}</april:textarea>
        </div>
    </div>

    <fieldset class="grid gap-3 rounded-lg border p-4 md:grid-cols-3">
        <legend class="px-1 text-sm font-medium">Automation policy</legend>
        <label class="flex items-start gap-3 text-sm">
            <input type="hidden" name="is_default" value="0">
            <input type="checkbox" name="is_default" value="1" class="mt-0.5 size-4 rounded border-input" {{ old('is_default', $calendarTemplate?->is_default) ? 'checked' : '' }}>
            <span><span class="font-medium">Organization default</span><span class="mt-1 block text-muted-foreground">Campuses inherit this unless they deliberately override it.</span></span>
        </label>
        <label class="flex items-start gap-3 text-sm">
            <input type="hidden" name="auto_open" value="0">
            <input type="checkbox" name="auto_open" value="1" class="mt-0.5 size-4 rounded border-input" {{ old('auto_open', $calendarTemplate?->auto_open) ? 'checked' : '' }}>
            <span><span class="font-medium">Open on start date</span><span class="mt-1 block text-muted-foreground">Scheduled cycles and periods open automatically. Closing remains manual.</span></span>
        </label>
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-1">
            <april:input-group name="generate_ahead_weeks" id="generate-ahead-weeks" type="number" min="0" max="104" label="Generate next cycle (weeks ahead)" value="{{ old('generate_ahead_weeks', $calendarTemplate?->generate_ahead_weeks ?? 0) }}" />
            <april:input-group name="remind_days_before" id="remind-days-before" type="number" min="0" max="90" label="Reminder lead time (days)" value="{{ old('remind_days_before', $calendarTemplate?->remind_days_before ?? 14) }}" />
        </div>
    </fieldset>

    <section class="space-y-3">
        <div>
            <h2 class="font-semibold">Cycle periods</h2>
            <p class="text-sm text-muted-foreground">Rows run in order. To make a sub-period, set its parent to an earlier row. Leave unused rows blank.</p>
        </div>
        <div class="overflow-x-auto rounded-lg border">
            <table class="min-w-[900px] w-full text-sm">
                <thead class="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>
                        <th class="px-3 py-2">#</th><th class="px-3 py-2">Name</th><th class="px-3 py-2">Local label</th><th class="px-3 py-2">Type</th><th class="px-3 py-2">Order</th><th class="px-3 py-2">Starts day</th><th class="px-3 py-2">Days</th><th class="px-3 py-2">Parent row</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($periods as $index => $period)
                        <tr>
                            <td class="px-3 py-2 text-muted-foreground">{{ $index + 1 }}</td>
                            <td class="p-2"><input name="periods[{{ $index }}][name]" value="{{ $period['name'] }}" class="w-full rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} name"></td>
                            <td class="p-2"><input name="periods[{{ $index }}][label]" value="{{ $period['label'] }}" class="w-full rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} local label"></td>
                            <td class="p-2"><select name="periods[{{ $index }}][type]" class="w-full rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} type">@foreach (\App\Enums\AcademicPeriodType::cases() as $type)<option value="{{ $type->value }}" {{ ($period['type'] ?? 'term') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>@endforeach</select></td>
                            <td class="p-2"><input name="periods[{{ $index }}][position]" type="number" min="1" max="99" value="{{ $period['position'] }}" class="w-20 rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} display order"></td>
                            <td class="p-2"><input name="periods[{{ $index }}][start_offset_days]" type="number" min="0" max="3660" value="{{ $period['start_offset_days'] }}" class="w-24 rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} start offset"></td>
                            <td class="p-2"><input name="periods[{{ $index }}][length_days]" type="number" min="1" max="3660" value="{{ $period['length_days'] }}" class="w-20 rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} length"></td>
                            <td class="p-2"><select name="periods[{{ $index }}][parent_index]" class="w-full rounded-md border border-input bg-background px-2 py-1.5" aria-label="Period {{ $index + 1 }} parent"><option value="">None</option>@for ($parent = 1; $parent <= $index; $parent++)<option value="{{ $parent }}" {{ (string) ($period['parent_index'] ?? '') === (string) $parent ? 'selected' : '' }}>Row {{ $parent }}</option>@endfor</select></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <april:button type="submit">{{ $calendarTemplate ? 'Save calendar template' : 'Create calendar template' }}</april:button>
</form>
