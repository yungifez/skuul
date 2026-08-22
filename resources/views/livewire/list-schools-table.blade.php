<div class="card" >
    <div class="card-header">
        <h4 class="card-title">All Schools</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\School::class" uniqueId="schoolTablepage" :empty-state="['heading' => 'No schools yet', 'description' => 'Add the first campus to begin school operations.', 'action' => ['href' => route('schools.create'), 'ability' => 'create', 'arguments' => [\App\Models\School::class], 'label' => 'Add school']]" :columns="
        [
            ['property' => 'name'] ,
            ['property' => 'initials' ],
            ['property' => 'address'],
            ['property' => 'code'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'schools.edit', 'text' => 'Settings', 'icon' => 'settings'],
                ['href' => 'schools.show', 'text' => 'View', 'icon' => 'eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'schools.destroy']
         ]
        "/>
    </div>
</div>
