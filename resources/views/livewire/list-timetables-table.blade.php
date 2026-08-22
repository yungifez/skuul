<div class="card">
    <div class="card-header">
        <div class="card-title">timetables List</div>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole(\App\Enums\Role::Student))
            <div class="flex w-full flex-col gap-2">
                <april:label for="my_class">Select a class to see timetable</april:label>
                <april:select id="my_class" name="" wire:model.live="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
            </div>

        @endif

        @isset($class)
        <div  wire:loading.remove.delay>
            <livewire:datatable :wire:key="Str::Random(10)" :model="App\Models\MyClass::class"
            :filters="[
                ['name' => 'find' ,'arguments' => [ $class]],
                ['name' => 'timetables'],
                ['name' => 'where' , 'arguments' =>[ 'academic_period_id' , current_academic_period_id()]]
            ]"
            :columns="[
                ['property' => 'name'],
                ['name' => 'Status', 'type' => 'timetable-status'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'timetables.show', 'text' => 'View', 'icon' => 'eye',  'can' => 'read timetable'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'timetables.destroy', 'can' => 'delete timetable']
            ]"
            />
        </div>
    @endisset
        <x-loading-spinner/>
    </div>
</div>
