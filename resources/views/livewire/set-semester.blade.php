<div class="card">
    <form action="{{route('semesters.set-semester')}}" method="POST" class="card-body">
        <x-display-validation-errors />
        <div class="flex w-full flex-col gap-2">
            <april:label for="set-semester-form">Change School Semester</april:label>
            <april:select name="semester_id" id="set-semester-form">
            @foreach ($semesters as $semester)
                <option @selected(current_semester_id() == $semester->id) value="{{ $semester->id }}"> {{ $semester->name }}</option>
            @endforeach

            </april:select>
            @error('semester_id')
                <p class="text-sm text-destructive">{{ $message }}</p>
            @enderror
        </div>
        @csrf
        <div class="my-6 flex justify-center items-center">
            <april:button class="m-auto w-full lg:w-3/12">
<x-lucide-key class="mr-2 size-4" />
                Set semester
            </april:button>
        </div>
    </form>
</div>

