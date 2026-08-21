<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create exam slot in {{$exam->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.store', $exam->id)}}" method="post" class="md:w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Exam slot name" placeholder="Enter Exam slot name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description" />
            </div>
            <april:input-group id="total-marks" name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark" type="number" />
            @csrf
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
