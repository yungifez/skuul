<div>
    @livewire('display-validation-error')
    <form wire:submit.prevent="loadStudents">
        <p class="text-bold">Please select class and section</p>
        <x-adminlte-select name="oldClass" label="Old class" wire:model="oldClass">
            @foreach ($classes as $class)
                <option value="{{$class['id']}}">{{$class['name']}}</option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-select name="oldSection" label="Old section" wire:init="loadInitialNewSections" wire:model="oldSection">
            @isset($oldSections)
                @foreach ($oldSections as $section)
                    <option value="{{$section['id']}}">{{$section['name']}}</option>
                @endforeach
            @endisset
        </x-adminlte-select>
        <x-adminlte-select name="newClass" label="New class" wire:model="newClass">
            @foreach ($classes as $class)
                <option value="{{$class['id']}}">{{$class['name']}}</option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-select name="newSection" label="New section" wire:init="loadInitialOldSections" wire:model="newSection">
            @isset($newSections)
                @foreach ($newSections as $section)
                    <option value="{{$section['id']}}">{{$section['name']}}</option>
                @endforeach
            @endisset
        </x-adminlte-select>
        <x-adminlte-button label="Fetch students" theme="primary" icon="fas fa-key" type="submit"/>
    </form>
    @if (isset($students))
        @if ($students->count() > 0)
            <form action="{{route('students.promote')}}" method="post" class="border border-primary my-3 p-3">
                <h3 class="text-bold text-center">Choose what happens with each student</h3>
                <input type="hidden" name="old_class" value="{{$oldClass}}">
                <input type="hidden" name="old_section" value="{{$oldSection}}">
                <input type="hidden" name="new_class" value="{{$newClass}}">    
                <input type="hidden" name="new_section" value="{{$newSection}}">
                @foreach ($students as $student)
                    <div class="form-group">
                        <x-adminlte-select name="student_id[]" id="student-{{$student->id}}" label="{{$student['name']}}">
                            <option value="{{$student['id']}}">Promote</option>
                            <option value="">Dont promote</option>
                        </x-adminlte-select>
                    </div>
                @endforeach
                @csrf
                <x-adminlte-button label="Promote students" theme="primary" icon="fas fa-key" type="submit"/>
            </form> 
        @else
            <x-adminlte-alert theme="danger" title="Danger" class="m-2">
                <p>No students found</p>
            </x-adminlte-alert>
        @endif
    @endif
</div>
