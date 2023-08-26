<div class="card">
    <div class="card-header">
        <h4 class="card-title">Section list</h4>
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
                <div wire:loading.remove.delay>
                    <livewire:datatable :wire:key="Str::random()" :model="App\Models\MyClass::class" uniqueId="section-list-table" :filters="[['name' => 'find' , 'arguments' => [$class]], ['name' => 'sections']]" :columns="
                        [
                        ['property' => 'name'] , 
                        ['type' => 'dropdown', 'name' => 'actions','links' => [
                            ['href' => 'sections.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                            ['href' => 'sections.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ]],
                        ['type' => 'delete', 'name' => 'Delete', 'action' => 'sections.destroy']
                    ]
                    "/>
                </div>
            @endisset
        @else
            <p>No classes and sections created in this school</p>
        @endif
    </div>
</div>
