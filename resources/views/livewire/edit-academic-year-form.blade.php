<div class="card">
    <div class="card-body">
        <form action="{{route('academic-years.update',$academicYear)}}" autocomplete="off" method="POST" class="md:w-6/12">
                <x-display-validation-errors/>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="start-year">Start year</april:label>
                    <april:select id="start-year" name="start_year" required x-data="{ years: [...Array(400)].map((_, i) => i + 1900) }">
                        <template x-for="year in years" :key="year">
                            <option :selected="{{ $academicYear->start_year }} == year" :value="year" x-text="year"></option>
                        </template>
                    </april:select>
                </div>
                <div class="flex w-full flex-col gap-2">
                    <april:label for="stop-year">Stop year</april:label>
                    <april:select id="stop-year" name="stop_year" required x-data="{ years: [...Array(400)].map((_, i) => i + 1900) }">
                        <template x-for="year in years" :key="year">
                            <option :selected="{{ $academicYear->stop_year }} == year" :value="year" x-text="year"></option>
                        </template>
                    </april:select>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" value="{{ $academicYear->starts_on?->toDateString() }}" />
                    <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" value="{{ $academicYear->ends_on?->toDateString() }}" />
                </div>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <april:button id="start-year" type="submit" class="w-full md:w-6/12">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
            </div>
        </form>
    </div>
</div>
