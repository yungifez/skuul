<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit exam slot {{$examSlot->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.update',[ $exam->id, $examSlot->id])}}" method="post">
            @livewire('display-validation-error')
            <h3 class="text-center">Edit exam slot in {{$exam->name}}</h3>
            <x-adminlte-input name="name" label="Exam slot Name" placeholder="Enter Exam slot name" fgroup-class="col-md-6" enable-old-support value="{{$examSlot->name}}"/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6" enable-old-support>
                {{$examSlot->description}}
            </x-adminlte-textarea>
            <x-adminlte-input name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark" fgroup-class="col-md-6" type="number" enable-old-support value="{{$examSlot->total_marks}}"/>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>