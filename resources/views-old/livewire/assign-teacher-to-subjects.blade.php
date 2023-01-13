<div class="card">
    <div class="card-header">
        <h4 class="card-title">Assign teachers to subjects</h4>
    </div>
    <div class="card-body">
        @include('livewire.loading-spinner')
        <form action="" wire:submit.prevent="fetchSubjects('{{$class}}', '{{$teacher}}')" class="row">
            <x-adminlte-select name="my_class_id" label="Select class" fgroup-class="col-md-6" wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
            <x-adminlte-select id="teacher_id" name="teacherId" label="Add teachers to class" fgroup-class="col-md-6" enable-old-support wire:model="teacher">
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach
            </x-adminlte-select>
            <div class="col-md-2">
                <x-adminlte-button label="Fetch Subjects" theme="primary" icon="fas fa-paper-plane" type="submit" />
            </div>
        </form>
        @isset($subjects)
            @if (!$subjects->isEmpty())
                <form action="{{route('subjects.assign-teacher-to-subject', $teacherState->id)}}" method="POST"  class="border border-primary my-3 p-3">
                    <h4 class="text-bold text-center">Add or remove subjects you want {{$teacherState->firstname()}} to manage</h4>
                    <div class="form-group">
                        @foreach ($subjects as $subject)
                            <x-adminlte-select name="subjects[]" id="subject-{{$subject->id}}" label="{{$subject['name']}}" fgroup-class="col-md-6">
                                <option value="{{$subject['id']}}">Include</option>
                                <option value="">Dont Include</option>
                            </x-adminlte-select>
                        @endforeach
                    </div>
                    @csrf
                    <x-adminlte-button label="Assign teacher to subjects" theme="primary" icon="fas fa-key" type="submit"/>
                </form>
            @else
                <p>No subjects in this class</p>
            @endif

        @endif
    </div>
</div>
