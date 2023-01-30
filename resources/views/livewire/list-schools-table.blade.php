<div class="card" >
    <div class="card-header">
        <h4 class="card-title">All Schools</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\School::class" uniqueId="schoolTablepage" :columns="
        [
            ['property' => 'name'] , 
            ['property' => 'initials' ], 
            ['property' => 'address'],
            ['property' => 'code'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'schools.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'schools.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'schools.destroy']
         ]
        "/>
    </div>
</div>
