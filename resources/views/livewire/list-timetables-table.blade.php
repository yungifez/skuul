<div class="card">
    <div class="card-header">
        <div class="card-title">timetables List</div>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole('student'))
            <x-select id="my_class" label="Select a class to see timetable"  group-class="my-6 md:w-1/2" name="" wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-select>
    
        @endif

        @isset($class)
        <div  wire:loading.remove.delay>
            <livewire:datatable :wire:key="Str::Random(10)" :model="App\Models\MyClass::class"
            :filters="[
                ['name' => 'find' ,'arguments' => [ $class]],
                ['name' => 'timetables'],
                ['name' => 'where' , 'arguments' =>[ 'semester_id' , auth()->user()->school->semester_id]]
            ]"
            :columns="[
                ['property' => 'name'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'timetables.show', 'text' => 'View', 'icon' => 'fas fa-eye',  'can' => 'read timetable'],
                    ['href' => 'timetables.edit', 'text' => 'Edit', 'icon' => 'fas fa-pen',  'can' => 'update timetable'],
                    ['href' => 'timetables.manage', 'text' => 'Build', 'icon' => 'fas fa-hammer',  'can' => 'update timetable'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'timetables.destroy', 'can' => 'delete timetable']
            ]"
            />
        </div>
    @endisset
        <x-loading-spinner/>
    </div>
</div>