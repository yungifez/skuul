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
            <x-input id="name" name="name" label="Exam Name *" placeholder="Enter Exam name"/>
            <x-textarea id="description" name="description" label="Description " placeholder="Enter description" />
            <div class="col-md-6">
                <x-input id="start_date" type="date" name="start_date" label="Start date *" required  value="{{old('start_date')}}"/>
            </div>
            <div class="col-md-6">
                <x-input type="date" id="date" name="stop_date" label="Stop date *" required value="{{old('stop_date')}}"/>
            </div>
            <x-select id="select" name="semester_id" label="Select Semester *"  wire:loading.attr="disabled" wire:target="semester" >
                @foreach ($semesters as $item)
                    <option value="{{$item['id']}}" @selected(auth()->user()->school->semester->id == $item['id'])>{{$item['name']}}</option>
                @endforeach
            </x-select>
            @csrf
            <x-button label="Create" icon="fas fa-key" type="submit" class="w-full md:w-6/12"/>
        </form>
    </div>
</div>