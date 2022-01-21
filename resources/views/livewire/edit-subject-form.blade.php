<div>
    @livewire('display-validation-error')
    <form action="{{route('subjects.update', $subject->id)}}" method="POST">
        <x-adminlte-input name="name" label="Subject Name" placeholder="Enter subject name" fgroup-class="col-md-6" value="{{$subject->name}}"/>
        <x-adminlte-input name="short_name" label="Subject Short Name" placeholder="Enter subject short name" fgroup-class="col-md-6" value="{{$subject->short_name}}"/>
        @csrf
        @method('PUT')
        <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
    </form>
</div>
