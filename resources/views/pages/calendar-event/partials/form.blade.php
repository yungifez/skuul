{{--
    The fields of one calendar event. The create and edit screens post to
    different routes but ask for exactly the same things, so the fields live
    here and neither screen can drift from the other.
--}}
@php
    $chosenSections = collect(old(
        'academic_cycle_section_ids',
        $event?->audiences->pluck('academic_cycle_section_id')->filter()->all() ?? [],
    ))->map(fn ($id) => (int) $id);
    $startsAt = old('starts_at', $event?->starts_at?->format('Y-m-d\TH:i') ?? $day->format('Y-m-d\T08:00'));
    $endsAt = old('ends_at', $event?->ends_at?->format('Y-m-d\TH:i') ?? $day->format('Y-m-d\T15:00'));
@endphp

<april:card>
    <slot:title>What is on</slot:title>
    <slot:description>
        A holiday or a closure means the school is shut. Attendance and the timetable both read that,
        so choose the kind carefully.
    </slot:description>
    <slot:content>
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="flex flex-col gap-2 lg:col-span-2">
                <april:label for="title">Title</april:label>
                <april:input id="title" name="title" value="{{ old('title', $event?->title) }}" required
                    placeholder="Mid-term break" />
                @error('title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-2">
                <april:label for="type">Kind of day</april:label>
                <april:native-select id="type" name="type" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $event?->type->value) === $type->value)>
                            {{ $type->label() }}{{ $type->isTeachingDay() ? '' : ' — the school is shut' }}
                        </option>
                    @endforeach
                </april:native-select>
                @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-2">
                <april:label for="location">Where</april:label>
                <april:input id="location" name="location" value="{{ old('location', $event?->location) }}" placeholder="Optional" />
                @error('location') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm lg:col-span-2">
                <input type="hidden" name="is_all_day" value="0">
                <input type="checkbox" name="is_all_day" value="1" @checked(old('is_all_day', $event?->is_all_day ?? true))
                    class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                This lasts all day. The times below are ignored.
            </label>

            <div class="flex flex-col gap-2">
                <april:label for="starts_at">Starts</april:label>
                <input type="datetime-local" id="starts_at" name="starts_at" value="{{ $startsAt }}" required
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                @error('starts_at') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-2">
                <april:label for="ends_at">Ends</april:label>
                <input type="datetime-local" id="ends_at" name="ends_at" value="{{ $endsAt }}" required
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                @error('ends_at') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col gap-2 lg:col-span-2">
                <april:label for="description">What it is</april:label>
                <textarea id="description" name="description" rows="3"
                    class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    placeholder="Optional">{{ old('description', $event?->description) }}</textarea>
                @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
            </div>
        </div>
    </slot:content>
</april:card>

<april:card>
    <slot:title>Who it is for</slot:title>
    <slot:description>
        Name no home group and the day is for the whole school. Name one or more and only those
        families read it in the portal.
    </slot:description>
    <slot:content>
        @if ($sections->isEmpty())
            <x-empty-state icon="lucide-users" title="This school has no home groups yet"
                description="The day will be for the whole school." />
        @else
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($sections as $section)
                    <label class="flex items-center gap-2 rounded-md border p-3 text-sm">
                        <input type="checkbox" name="academic_cycle_section_ids[]" value="{{ $section->id }}"
                            @checked($chosenSections->contains($section->id))
                            class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring">
                        {{ $section->name }}
                    </label>
                @endforeach
            </div>
            @error('academic_cycle_section_ids.*') <p class="mt-2 text-sm text-destructive">{{ $message }}</p> @enderror
        @endif
    </slot:content>
</april:card>
