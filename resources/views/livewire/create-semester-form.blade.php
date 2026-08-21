<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create semester in session {{current_academic_year()->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('semesters.store')}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Semester Name" placeholder="Enter semester name" />
            <div class="grid gap-4 md:grid-cols-2">
                <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" />
                <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="type">Period type</april:label>
                <april:select id="type" name="type">
                <option value="semester">Semester</option>
                <option value="term">Term</option>

                </april:select>
                @error('type')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
            </div>
        </form>
    </div>
</div>
