<form action="{{route('sections.update', $section->id)}}" method="POST">
    @livewire('display-validation-error')
    <x-adminlte-input name="name" label="Section name" placeholder="Enter section name" fgroup-class="col-md-6" value="{{$section->name}}"/>
    @csrf
    <x-adminlte-input name="class" label="Section class" placeholder="Enter section class" fgroup-class="col-md-6" value="{{$section->myClass->name}}" disabled/>
    @method('put')
    <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
</form>