<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Student</h3>
    </div>
    <div class="card-body">
        <form action="{{route('students.store')}}" method="POST" enctype="multipart/form-data">
            @livewire('create-user-fields', ['role' => 'Student'])
            <div class="row">
                <h4 class="text-bold col-12 text-center">Class information</h4>
                <x-adminlte-select name="my_class_id" label="Choose a class" fgroup-class="col-md-6" wire:model="myClass">
                    @foreach ($myClasses as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                </x-adminlte-select>
                <x-adminlte-select name="section_id" label="Choose a section" fgroup-class="col-md-6" wire:model="section" wire:init="loadInitialSections">
                    @if (isset($sections))
                        @foreach ($sections as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    @else
                        <option value="" disabled>Select a class first</option>
                    @endif
                </x-adminlte-select>
                <x-adminlte-input name="admission_number" label="Admission number ( would be automatically created if left blank )" placeholder="Student's admission number" fgroup-class="col-md-6" enable-old-support/>
                <x-adminlte-input-date name="admission_date" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose student's admission date..." label="Date of admission"  fgroup-class="col-md-6" value="{{old('admission_date')}}"/>
                @csrf
                <div class='col-12 my-2'>
                    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
                </div>
            </div>
        </form>
    </div>
</div>
