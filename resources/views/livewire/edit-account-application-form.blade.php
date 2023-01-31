<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit application</h1>
    </div>
    <div class="card-body">
        <form action="{{route('account-applications.update', $applicant->id)}}" method="POST" enctype="multipart/form-data">
            <livewire:edit-user-fields :user="$applicant" />
            <x-select id="role" name="role_id" label="Change role">    
                @foreach ($roles as $item)
                    <option value="{{$item['id']}}" @selected($applicant->accountApplication && $applicant->accountApplication ? $item['id'] == $applicant->accountApplication->role->id :  null) >{{$item['name']}}</option>
                @endforeach
            </x-select>
            @csrf
            @method('PUT')
            <x-button label="Edit Application" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>
