<div class="row">
    <h4 class="text-bold col-12 text-center">Class information</h4>
    <x-adminlte-select name="my_class_id" label="Choose a class" fgroup-class="col-md-6" wire:model="myClass">
        @foreach ($myClasses as $item)
            <option value="{{$item['id']}}">{{$item['name']}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-select name="section_id" label="Choose a section" fgroup-class="col-md-6" wire:model="section">
        @if (isset($sections))
            @foreach ($sections as $item)
                <option value="{{$item['id']}}">{{$item['name']}}</option>
            @endforeach
        @else
            <option value="" disabled>Select a class first</option>
        @endif
    </x-adminlte-select>
    <x-adminlte-input name="admission_number" label="Admission number ( would be automatically created if left blank )" placeholder="Student's admission number" fgroup-class="col-md-6" enable-old-support autocomplete="off"/>
    <x-adminlte-input-date name="admission_date" :config="['format' => 'YYYY/MM/DD']" placeholder="Choose student's admission date..." label="Date of admission"  fgroup-class="col-md-6" value="{{old('admission_date')}}"  autocomplete="off"/>
</div>
