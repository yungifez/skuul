@isset($roles)
    <form action="{{route('register')}}" method="POST" enctype="multipart/form-data" class="w-full">
        <div class="card-body" >
            <x-select id="role" name="role" label="Register as" class="capitalize">    
                    @foreach ($roles as $item)
                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                    @endforeach
            </x-select>
            <x-select id="school" name="school" label="School" class="text-capitalize">    
                    @foreach ($schools as $item)
                        <option value="{{$item['id']}}" >{{$item['name']}} - {{$item['address']}}</option>
                    @endforeach
            </x-select>
            <livewire:create-user-fields/>
            @csrf
            <x-button label="Register" icon="fas fa-key" type="submit" class="w-full md:w-3/12"/>
        </div>
        <hr>
    </form>
@else
   <p>Couldn't create user, Roles not found.</p> 
@endisset
