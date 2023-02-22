<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit {{$fee->name}}</h2>
    </div>
    <div class="card-body">
        <form action="{{route('fee-categories.update', $fee->id)}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Name" placeholder="Fee Name" value="{{$fee->name}}"/>
            <x-textarea id="description" name="description" placeholder="Fee Description" label="Description">
                {{$fee->description}}
            </x-textarea>
            <x-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="w-full md:w-1/2"/>
            @csrf
            @method('PUT')
        </form>
    </div>
</div>
