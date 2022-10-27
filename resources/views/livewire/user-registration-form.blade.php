<div class="card-body">
    @isset($roles)
    <form action="{{route('register')}}" method="POST" enctype="multipart/form-data">
        <div class="card-body" >
            <x-adminlte-select wire:model="role" name="role" label="Register as" enable-old-support class="text-capitalize">    
                    @foreach ($roles as $item)
                        <option value="{{$item['id']}}" >{{$item['name']}}</option>
                    @endforeach
            </x-adminlte-select>
            <x-adminlte-select wire:model="school" name="school" fgroup-class="" label="School" enable-old-support class="text-capitalize">    
                    @foreach ($schools as $item)
                        <option value="{{$item['id']}}" >{{$item['name']}}</option>
                    @endforeach
            </x-adminlte-select>
            <div class="d-flex justify-content-center">
                <div wire:loading class="spinner-border" role="status">
                    <p class="sr-only">Loading.....</p>
                </div>
            </div>
            
            <div>
                @switch($roleName)
                    @case('student')
                        @livewire('create-student-form', ['school' => $school,'actionURL' => 
                    route('register'), 'includeFormTag' => false], key(Str::random(10)))
                        @break
                    @case('teacher')
                        @livewire('create-teacher-form', ['actionURL' => 
                        route('register'), 'includeFormTag' => false], key(Str::random(10)))
                        @break
                    @case('parent')
                        @livewire('create-parent-form', ['actionURL' => 
                        route('register'), 'includeFormTag' => false], key(Str::random(10)))
                        @break
                @default
                    Role not found
                @endswitch
            </div>
        </div>
    </form>
    @else
        Couldn't create user, Roles not found.
    @endisset
</div>
