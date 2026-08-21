<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$section->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('sections.update', $section->id)}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors />
            <april:input-group id="name" name="name" label="Section name" placeholder="Enter section name" value="{{$section->name}}" />
            @csrf
            <april:input-group id="class" name="class" label="Section class" placeholder="Enter section class" value="{{$section->myClass->name}}" disabled />
            @method('put')
            <april:button type="submit" class="w-full md:w:1/2">
                <x-lucide-key class="mr-2 size-4" />
                Edit
            </april:button>
        </form>
    </div>
</div>
