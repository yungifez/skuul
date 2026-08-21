<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit exam slot {{$examSlot->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.update',[ $exam->id, $examSlot->id])}}" method="post" class="w-1/2">
            <x-display-validation-errors/>
            <april:input-group id="name" name="name" label="Exam slot Name" placeholder="Enter Exam slot name" value="{{$examSlot->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description">{{$examSlot->description}}</april:textarea>
            </div>
            <april:input-group id="total_marks" name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark" type="number" value="{{$examSlot->total_marks}}" />
            @csrf
            @method('PUT')
            <april:button type="submit" class="w-full md:w-1/2">
                <x-lucide-pencil class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>
