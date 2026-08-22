<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$academicPeriod->name}} in session {{current_academic_year()->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('academic-periods.update', $academicPeriod->id)}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="AcademicPeriod Name" placeholder="Enter academic period name" value="{{$academicPeriod->name}}" />
            <div class="grid gap-4 md:grid-cols-2">
                <april:input-group id="starts-on" name="starts_on" type="date" label="Starts on" value="{{ $academicPeriod->starts_on?->toDateString() }}" />
                <april:input-group id="ends-on" name="ends_on" type="date" label="Ends on" value="{{ $academicPeriod->ends_on?->toDateString() }}" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="type">Period type</april:label>
                <april:select id="type" name="type">
                @foreach (\App\Enums\AcademicPeriodType::cases() as $type)
                    <option value="{{ $type->value }}" @selected($academicPeriod->type === $type)>{{ $type->label() }}</option>
                @endforeach

                </april:select>
                @error('type')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            @method('PUT')
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>
