<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create syllabus</h3>
    </div>
    <div class="card-body">
        <form action="{{route('syllabi.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            @livewire('display-validation-error')
            <x-adminlte-select name='my_class_id' id="my_class" label="Class"  fgroup-class="col-md-6" enable-old-support wire:model="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
            <x-adminlte-select name='subject_id' id="subject" label="Subject" wire:init="loadInitialSubjects" fgroup-class="col-md-6" enable-old-support wire:model="subject" >
                @isset($subjects)
                    @foreach ($subjects as $subject)
                        <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
            <x-adminlte-input name="name" id="name" label="Name" fgroup-class="col-md-6" enable-old-support placeholder="Name (Eg: Physics second semester syllabus) "/>
            <x-adminlte-textarea name="description" placeholder="Insert description (optional)... " fgroup-class="col-md-6" label="Description" rows=5/>
            <x-adminlte-input-file name="file" acept="pdf/*" label="Upload file" placeholder="Choose a PDF file..." fgroup-class="col-md-6"/>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>