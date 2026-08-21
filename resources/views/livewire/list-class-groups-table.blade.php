<div class="card" >
    <div class="card-header">
        <h4 class="card-title">All Class Groups</h4>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\ClassGroup::class" uniqueId="class-group-list" :filters="[['name' => 'inSchool']]" :columns="
        [
            ['property' => 'name'] ,
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'class-groups.edit', 'text' => 'Settings', 'icon' => 'settings'],
                ['href' => 'class-groups.show', 'text' => 'View', 'icon' => 'eye'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'class-groups.destroy']
         ]
        "/>
    </div>
</div>
