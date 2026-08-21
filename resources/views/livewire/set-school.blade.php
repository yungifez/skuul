<april:card>
    <slot:title class="flex items-center gap-3 text-base">
        <span class="flex size-9 items-center justify-center rounded-md bg-muted">
            <x-icon name="lucide-map-pin" class="size-4" />
        </span>
        Working school
    </slot:title>
    <slot:description>Choose the school context for your next action.</slot:description>
    <slot:content>
        <form action="{{route('schools.setSchool')}}" method="POST" class="space-y-5">
            <x-display-validation-errors />
            <div class="flex w-full flex-col gap-2">
                <april:label for="set-school-form">School branch</april:label>
                <april:select name="school_id" id="set-school-form">
                @foreach ($schools as $school)
                <option @selected(current_school_id()==$school->id) value="{{ $school->id }}" @selected(current_school_id()
                    == $school->id)> {{ $school->name }} - {{$school->address}}</option>
                @endforeach

                </april:select>
                @error('school_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <div class="flex justify-end">
                <april:button class="w-full sm:w-auto">
                    Set working school
                </april:button>
            </div>
        </form>
    </slot:content>
</april:card>
