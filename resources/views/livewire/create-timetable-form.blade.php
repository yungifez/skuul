<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.store')}}" method="POST" class="md:w-1/2">
            @csrf
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <april:input-group wire:ignore id="name" name="name" label="Timetable name *" placeholder="Enter timetable name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <label for="academic-cycle-section" class="text-sm font-medium">Home group *</label>
                <select id="academic-cycle-section" name="academic_cycle_section_id" required class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @forelse ($cycleSections as $cycleSection)
                        <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['label'] }}</option>
                    @empty
                        <option value="" disabled selected>No active home groups in this academic cycle</option>
                    @endforelse
                </select>
                @error('academic_cycle_section_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
        </form>
    </div>
</div>
