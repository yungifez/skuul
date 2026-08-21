<div class="md:grid grid-cols-12 gap-2">
    <h4 class="text-bold text-xl md:text-3xl font-bold col-span-12 text-center my-2">Class information</h4>
    <div class="flex w-full flex-col gap-2">
        <april:label for="class-id">Choose a class *</april:label>
        <april:select id="class-id" name="my_class_id" wire:model.live="myClass">
        @foreach ($myClasses as $item)
            <option value="{{$item['id']}}">{{$item['name']}}</option>
        @endforeach

        </april:select>
        @error('my_class_id')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <div class="flex w-full flex-col gap-2">
        <april:label for="class-id">Choose a section *</april:label>
        <april:select id="class-id" name="section_id" wire:model.live="section">
        @if (isset($sections))
            @foreach ($sections as $item)
                <option value="{{$item['id']}}">{{$item['name']}}</option>
            @endforeach
        @else
            <option value="" disabled>Select a class first</option>
        @endif

        </april:select>
        @error('section_id')
            <p class="text-sm text-destructive">{{ $message }}</p>
        @enderror
    </div>
    <april:input-group id="admission-number" name="admission_number" label="Admission number" placeholder="Student's admission number" />
    <april:input-group type="date" id="admission-date" name="admission_date" placeholder="Choose student's admission date..." label="Date of admission  *" value="{{old('admission_date')}}" autocomplete="off" wire:ignore />
</div>
