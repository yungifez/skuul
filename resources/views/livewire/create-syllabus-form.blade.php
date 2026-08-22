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
            <div class="flex w-full flex-col gap-2">
                <april:label for="my_class">Class *</april:label>
                <april:select id="class" name='my_class_id' id="my_class" wire:model.live="class">
                @isset($classes)
                    @foreach ($classes as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('my_class_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="subject">Subject *</april:label>
                <april:select id="subject" name='subject_id' id="subject" wire:init="loadInitialSubjects" wire:model.live="subject">
                @isset($subjects)
                    @foreach ($subjects as $subject)
                        <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                    @endforeach
                @endisset

                </april:select>
                @error('subject_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <april:input-group id="name" name="name" id="name" label="Name *" placeholder="Name (Eg: Physics second academic period syllabus) " wire:ignore />
            <div class="flex w-full flex-col gap-2 md:col-span-6">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Insert description (optional)..." rows="5" />
            </div>
            <april:input-group id="file" type="file" name="file" acept="pdf/*" label="Upload file *" placeholder="Choose a PDF file..." fgroup-class="col-md-6" />
            <april:button type="submit" class="w-full md:w-6/12">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
