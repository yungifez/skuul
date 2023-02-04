<div class="md:grid grid-cols-12 gap-2">
    <h4 class="text-bold text-xl md:text-3xl font-bold col-span-12 text-center my-2">Class information</h4>
    <x-select id="class-id" name="my_class_id" label="Choose a class *" group-class="col-span-6" wire:model="myClass">
        @foreach ($myClasses as $item)
            <option value="{{$item['id']}}">{{$item['name']}}</option>
        @endforeach
    </x-select>
    <x-select id="class-id" name="section_id" label="Choose a section *" group-class="col-span-6" wire:model="section">
        @if (isset($sections))
            @foreach ($sections as $item)
                <option value="{{$item['id']}}">{{$item['name']}}</option>
            @endforeach
        @else
            <option value="" disabled>Select a class first</option>
        @endif
    </x-select>
    <x-input id="admission-number" name="admission_number" label="Admission number *" placeholder="Student's admission number" group-class="col-span-6" />
    <x-input type="date" id="admission-date" name="admission_date" placeholder="Choose student's admission date..." label="Date of admission"  group-class="col-span-6" value="{{old('admission_date')}}"  autocomplete="off" wire:ignore />
</div>
