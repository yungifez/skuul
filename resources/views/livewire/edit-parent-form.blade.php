<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit parent form</h3>
    </div>
    <div class="card-body">
        <form action="{{route('parents.update', $parent->id)}}" method="POST" enctype="multipart/form-data">
            @livewire('edit-user-fields', ['role' => 'Parent', 'user'=> $parent]
            )
                @csrf
                @method('PUT')
                <div class='col-12 my-2'>
                    <x-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="w-full md:w-3/12"/>
                </div>
        </form>
    </div>
</div>