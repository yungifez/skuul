<div class="card">
    <div class="card-header">
        <h4 class="card-title"> Grade systems list</h4>
    </div>
    <div class="card-body">
        @if (!auth()->user()->hasRole('student'))

        <x-select id="class-group" label="Select a class group to see grading system" group-class="my-6 md:w-6/12" name="" wire:model="classGroup">
            @foreach ($classGroups as $item)
                <option value="{{$item['id']}}">{{$item['name']}}</option>
            @endforeach
        </x-select>
        @endif
        <x-loading-spinner/>
        <div wire:loading.remove.delay>
            @isset($classGroup)
                <livewire:datatable :wire:key="Str::Random(10)" :model="App\Models\GradeSystem::class" 
                :filters="[
                ['name' => 'where', 'arguments' => ['class_group_id' , $classGroup]],
                ]"
                uniqueId="list-grades-table"
                :columns="[
                    ['property' => 'name'],
                    ['property' => 'remark'],
                    ['property' => 'grade_from'],
                    ['property' => 'grade_till'],
                    ['type' => 'dropdown', 'name' => 'actions',  'can' => 'update grade system','links' => [
                        ['href' => 'grade-systems.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                    ]],
                    ['type' => 'delete',  'can' => 'delete grade system' ,  'name' => 'Delete', 'action' => 'grade-systems.destroy']
                ]"/>
            @endisset
        </div>
    </div>
</div>
