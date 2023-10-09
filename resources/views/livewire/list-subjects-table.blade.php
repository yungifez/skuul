<div class="card">
    <div class="card-header">
        <h4 class="card-title">Subject List</h4>
    </div>
    <div class="card-body">
        @if ($classes->isNotEmpty())
            <x-select name="" id="class-select" class="md:w-6/12 my-4" wire:model.live="class">
                @foreach ($classes as $item)
                    <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
            </x-select>
            <div>
                <x-loading-spinner wire:target="class" />
            </div>
            @isset($class)
                <div  wire:loading.remove.delay>
                    <livewire:datatable unique_id="list-subject-table" :wire:key="Str::Random(10)" :model="App\Models\Subject::class"
                    :filters="[
                        ['name' => 'where' ,'arguments' => ['my_class_id', $class]],
                        ['name' => 'with', 'arguments' => ['teachers']]
                    ]"
                    :columns="[
                        ['property' => 'name'],
                        ['property' => 'short_name'],
                        ['name' => 'Number of teachers assigned', 'method' => 'count', 'relation' => 'teachers'],
                        ['type' => 'dropdown', 'name' => 'actions','links' => [
                            ['href' => 'subjects.edit', 'text' => 'Edit', 'icon' => 'fas fa-cog'],
                        ]],
                        ['type' => 'delete', 'name' => 'Delete', 'action' => 'subjects.destroy',]
                    ]"
                    />
                </div>
            @endisset
        @else
            <p>No classes and sections created in this school</p>
        @endif
    </div>
</div>
