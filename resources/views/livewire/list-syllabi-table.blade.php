<div class="card">
    <div class="card-header">
        <div class="card-title">Syllabi List</div>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole(\App\Enums\Role::Student))
            <div class="flex w-full flex-col gap-2">
                <april:label for="my_class">Select a class to see syllabus</april:label>
                <april:select id="my_class" name="" wire:model.live="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach

                </april:select>
            </div>

        @endif

        @isset($class)
        <div  wire:loading.remove.delay>
            <livewire:datatable unique_id="list-sylabii-table" :wire:key="Str::Random(10)" :model="App\Models\MyClass::class"
            :filters="[
                ['name' => 'find' ,'arguments' => [ $class]],
                ['name' => 'syllabi'],
                ['name' => 'where' , 'arguments' =>[ 'semester_id' , current_semester_id()]]
            ]"
            :columns="[
                ['property' => 'name'],
                ['type' => 'dropdown', 'name' => 'actions','links' => [
                    ['href' => 'syllabi.show', 'text' => 'View', 'icon' => 'eye', 'can' => 'read syllabus'],
                ]],
                ['type' => 'delete', 'name' => 'Delete', 'action' => 'syllabi.destroy', 'can' => 'delete syllabus']
            ]"
            />
        </div>
    @endisset
        <x-loading-spinner/>
    </div>
</div>
