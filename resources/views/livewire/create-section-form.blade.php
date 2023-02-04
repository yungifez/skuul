<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create section</h3>
    </div>
    <div class="card-body">
        <form action="{{route('sections.store')}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Section name" placeholder="Enter section name" />
            <x-select id="class_id" name="my_class_id" fgroup-class="col-md-6 mx12" label="Choose class" >
                @foreach ($myClasses as $myClass)
                    <option value="{{$myClass->id}}">{{$myClass->name}}</option>
                @endforeach
            </x-select>
            @csrf
            <div class="col-md-3">
                <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-6/12"/>
            </div>
        </form>
    </div>
</div>