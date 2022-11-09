<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Student account</h3>
    </div>
    <div class="card-body">
        @if ($includeFormTag = true)
        <form action="{{route('students.store')}}" method="POST" enctype="multipart/form-data">
        @endif
            @livewire('create-user-fields', ['role' => 'Student'])
            @livewire('create-student-record-fields')
            @csrf
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
         @if ($includeFormTag = true)
        </form>
        @endif
    </div>
</div>
