<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create exam </h3>
    </div>
    <div class="card-body">
        <form action="{{route('exams.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <p class="text-secondary">
                {{__('All fields marked * are required')}}
            </p>
            <april:input-group id="name" name="name" label="Exam Name *" placeholder="Enter Exam name" />
            <div class="flex w-full flex-col gap-2">
                <april:label for="description">Description</april:label>
                <april:textarea id="description" name="description" placeholder="Enter description" />
            </div>
            <div class="col-md-6">
                <april:input-group id="start_date" type="date" name="start_date" label="Start date *" required value="{{old('start_date')}}" />
            </div>
            <div class="col-md-6">
                <april:input-group type="date" id="date" name="stop_date" label="Stop date *" required value="{{old('stop_date')}}" />
            </div>
            <div class="flex w-full flex-col gap-2">
                <april:label for="select">Select AcademicPeriod *</april:label>
                <april:select id="select" name="academic_period_id" wire:loading.attr="disabled" wire:target="academicPeriod">
                @foreach ($academicPeriods as $item)
                    <option value="{{$item['id']}}" @selected(current_academic_period()->id == $item['id'])>{{$item['name']}}</option>
                @endforeach

                </april:select>
                @error('academic_period_id')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @csrf
            <april:button type="submit" class="w-full md:w-6/12">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>
        </form>
    </div>
</div>
