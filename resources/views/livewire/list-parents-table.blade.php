<div class="card">
    <div class="card-header">
        <h2 class="card-title">Parents list</h2>
    </div>
    <div class="card-body">
        <livewire:datatable :model="App\Models\User::class" uniqueId="parents-list-table" :filters="[['name' => 'role', 'arguments' => ['parent']], ['name' => 'ofSchool'], ['name' => 'orderBy' , 'arguments' => ['name']]]" :empty-state="['heading' => 'No parents yet', 'description' => 'Add the first parent for this school.', 'action' => ['href' => route('parents.create'), 'ability' => 'create', 'arguments' => [\App\Models\User::class, 'parent'], 'label' => 'Add parent']]" :columns="[
            ['property' => 'name'] ,
            ['property' => 'email'] ,
            ['property' => 'gender'] ,
            ['name' => 'Account', 'type' => 'account-status'],
            ['type' => 'dropdown', 'name' => 'actions','links' => [
                ['href' => 'parents.edit', 'text' => 'Manage Profile', 'icon' => 'pencil',],
                ['href' => 'parents.show', 'text' => 'View', 'icon' => 'eye',  ],
                ['href' => 'parents.assign-student', 'text' => 'Assign students', 'icon' => 'users'],
            ]],
            ['type' => 'delete', 'name' => 'Delete', 'action' => 'parents.destroy',]
         ]
        "/>
    </div>
</div>
