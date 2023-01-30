<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit exam slot {{$examSlot->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.update',[ $exam->id, $examSlot->id])}}" method="post" class="w-1/2">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Exam slot Name" placeholder="Enter Exam slot name"  value="{{$examSlot->name}}"/>
            <x-textarea id="description" name="description" label="Description" placeholder="Enter description"  >
                {{$examSlot->description}}
            </x-textarea>
            <x-input id="total_marks" name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark" type="number"  value="{{$examSlot->total_marks}}"/>
            @csrf
            @method('PUT')
            <x-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>