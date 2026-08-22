<div class="card">
    <form action="{{route('academic-periods.set-academic-period')}}" method="POST" class="card-body">
        <x-display-validation-errors />
        <div class="flex w-full flex-col gap-2">
            <april:label for="set-academic-period-form">Change School AcademicPeriod</april:label>
            <april:select name="academic_period_id" id="set-academic-period-form">
            @foreach ($academicPeriods as $academicPeriod)
                <option @selected(current_academic_period_id() == $academicPeriod->id) value="{{ $academicPeriod->id }}"> {{ $academicPeriod->name }}</option>
            @endforeach
            </april:select>
            @error('academic_period_id')
                <p class="text-sm text-destructive">{{ $message }}</p>
            @enderror
        </div>
        @csrf
        <div class="my-6 flex justify-center items-center">
            <april:button class="m-auto w-full lg:w-3/12">
<x-lucide-key class="mr-2 size-4" />
                Set academicPeriod
            </april:button>
        </div>
    </form>
</div>
