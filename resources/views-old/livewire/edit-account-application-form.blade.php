<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit application</h1>
    </div>
    <div class="card-body">
        <form action="{{route('account-applications.update', $applicant->id)}}" method="POST" enctype="multipart/form-data">
            @livewire('edit-user-fields', ['user' => $applicant])
            <x-adminlte-select name="role_id" label="Change role" enable-old-support class="text-capitalize">    
                @foreach ($roles as $item)
                    <option value="{{$item['id']}}" @selected($applicant->accountApplication && $applicant->accountApplication ? $item['id'] == $applicant->accountApplication->role->id :  null) >{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit Application" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>
