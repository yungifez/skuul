<div>
    @can('set academic year')
        <april:card>
            <slot:title class="flex items-center gap-3 text-base">
                <span class="flex size-9 items-center justify-center rounded-md bg-muted">
                    <x-icon name="lucide-calendar-range" class="size-4" />
                </span>
                Academic period
            </slot:title>
            <slot:description>Set the academic year used across this workspace.</slot:description>
            <slot:content>
                <form action="{{route('academic-years.set-academic-year')}}" method="POST" class="space-y-5">
                    <x-display-validation-errors/>
                    <div class="flex w-full flex-col gap-2">
                        <april:label for="name">Academic year</april:label>
                        <april:select id="name" name="academic_year_id">
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($academicYear->id == current_academic_year_id() )> {{ $academicYear->name}}</option>
                        @endforeach

                        </april:select>
                        @error('academic_year_id')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                    @csrf
                    <div class="flex justify-end">
                        <april:button class="w-full sm:w-auto" type="submit">
                            <x-icon name="lucide-calendar-check" class="mr-2 size-4" />
                            Set academic year
                        </april:button>
                    </div>
                </form>
            </slot:content>
        </april:card>
    @endcan
</div>
