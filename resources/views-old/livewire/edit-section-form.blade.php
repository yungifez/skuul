<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$section->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('sections.update', $section->id)}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Section name" placeholder="Enter section name" fgroup-class="col-md-6" value="{{$section->name}}" enable-old-support/>
            @csrf
            <x-adminlte-input name="class" label="Section class" placeholder="Enter section class" fgroup-class="col-md-6" value="{{$section->myClass->name}}" disabled/>
            @method('put')
            <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
        </form>
    </div>
</div>