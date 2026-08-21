<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.store')}}" method="POST" class="md:w-1/2">
            @csrf
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <april:input-group wire:ignore id="name" name="name" label="Timetable name *" placeholder="Enter timetable name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="class">Select class *</april:label>
                <april:select id="class" name="my_class_id" wire:model.live="class" wire:loading.attr="disabled" wire:target="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-key class="mr-2 size-4" />
                    Create
                </april:button>
        </form>
    </div>
</div>
