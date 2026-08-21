<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit exam {{$exam->id}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exams.update',$exam)}}" autocomplete="off" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Exam Name" placeholder="Enter semester name" value="{{$exam->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description">{{$exam->description}}</april:textarea>
            </div>
            <april:input-group id="start_date" type="date" name="start_date" label="Start date" required value="{{$exam->start_date}}" />
            <april:input-group type="date" id="stop_date" name="stop_date" label="Stop date" required value="{{$exam->stop_date}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="semster">Select Semester</april:label>
                <april:select id="semster" name="semester_id" wire:loading.attr="disabled" wire:target="semester">
                @foreach ($semesters as $semester)
                    <option value="{{$semester['id']}}" @selected($semester->id == $exam->semester_id )> {{$semester['name']}}</option>
                @endforeach

                </april:select>
                @error('semester_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            @method('PUT')
                <april:button type="submit" class="md:w-1/2 w-full">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
        </form>
    </div>
</div>
