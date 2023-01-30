<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create syllabus</h3>
    </div>
    <div class="card-body">
        <form action="{{route('syllabi.store')}}" method="POST" enctype="multipart/form-data" class="md:w-1/2">
            @csrf
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <x-select id="class" name='my_class_id' id="my_class" label="Class *" wire:model="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-select id="subject" name='subject_id' id="subject" label="Subject *" wire:init="loadInitialSubjects" wire:model="subject" >
                @isset($subjects)
                    @foreach ($subjects as $subject)
                        <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                    @endforeach
                @endisset
            </x-select>
            <x-input id="name" name="name" id="name" label="Name *"  placeholder="Name (Eg: Physics second semester syllabus) " wire:ignore/>
            <x-textarea id="description" name="description" placeholder="Insert description (optional)... " fgroup-class="col-md-6" label="Description" rows="5"/>
            <x-input id="file" type="file" name="file" acept="pdf/*" label="Upload file *" placeholder="Choose a PDF file..." fgroup-class="col-md-6"/>
            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-6/12"/>
        </form>
    </div>
</div>