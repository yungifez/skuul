<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.update',$timetable)}}" method="POST" class="md:w-1/2">
            @csrf
            @method('PUT')
            <april:input-group id="name" name="name" label="Timetable name" placeholder="Enter timetable name" value="{{$timetable->name}}" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description">{{$timetable->description}}</april:textarea>
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="class">Select class</april:label>
                <april:select id="class" name="my_class_id" wire:model.live="class" wire:loading.attr="disabled" wire:target="class" disabled>
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class='col-12 my-2'>
                <april:button type="submit" class="w-full md:w-1/2">
                    <x-lucide-pencil class="mr-2 size-4" />
                    Edit
                </april:button>
            </div>
        </form>
    </div>
</div>
