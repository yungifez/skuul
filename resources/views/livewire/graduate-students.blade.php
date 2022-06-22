<div class="card">
    <div class="card-header">
        <h4 class="card-title">Graduate student </h4>
    </div>
    <div class="card-body">
        @livewire('display-validation-error')
        <form wire:submit.prevent="loadStudents">
            <p class="text-bold">Please select class and section</p>
            <x-adminlte-select name="class" label="Class" wire:model="class" fgroup-class="col-md-6">
                @foreach ($classes as $class)
                    <option value="{{$class['id']}}">{{$class['name']}}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select name="section" label="Section" wire:init="loadInitialSections" wire:model="section" fgroup-class="col-md-6">
                @isset($sections)
                    @foreach ($sections as $section)
                        <option value="{{$section['id']}}">{{$section['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
           
            <x-adminlte-button label="Fetch students" theme="primary" icon="fas fa-key" type="submit"/>
        </form>
        @if (isset($students))
            @if ($students->count() > 0)
                <form action="{{route('students.graduate')}}" method="post" class="border border-primary my-3 p-3">
                    <h3 class="text-bold text-center">Choose what happens with each student</h3>
                    @foreach ($students as $student)
                        <div class="form-group">
                            <x-adminlte-select name="student_id[]" id="student-{{$student->id}}" label="{{$student['name']}}" fgroup-class="col-md-6">
                                <option value="{{$student['id']}}">Graduate</option>
                                <option value="">Dont Graduate</option>
                            </x-adminlte-select>
                        </div>
                    @endforeach
                    @csrf
                    <x-adminlte-button label="Graduate
                     students" theme="primary" icon="fas fa-key" type="submit"/>
                </form> 
            @else
                <x-adminlte-alert theme="danger" title="Danger" class="m-2">
                    <p>No students found</p>
                </x-adminlte-alert>
            @endif
        @endif
    </div>
</div>
