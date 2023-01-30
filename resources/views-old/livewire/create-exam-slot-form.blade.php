<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create exam slot in {{$exam->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exam-slots.store', $exam->id)}}" method="post">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Exam slot Name" placeholder="Enter Exam slot name" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-input name="total_marks" label="Maximum marks obtainable" placeholder="Enter max mark" fgroup-class="col-md-6" type="number" enable-old-support/>
            @csrf
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>